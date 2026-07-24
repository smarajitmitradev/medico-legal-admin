<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    private function getApi()
    {
        return new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function index(Request $request)
    {
        $subscriptions = $this->filteredQuery($request)->paginate(15)->withQueryString();

        $stats = [
            'total'     => Subscription::count(),
            'active'    => Subscription::where('status', 'paid')
                ->where('expiry_date', '>=', Carbon::now())->count(),
            'expired'   => Subscription::where('status', 'paid')
                ->where('expiry_date', '<', Carbon::now())->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'halted'    => Subscription::where('status', 'halted')->count(),
            'refunded'  => Subscription::whereIn('status', ['refunded', 'refund_initiated'])->count(),
            'revenue'   => Subscription::where('status', 'paid')->sum('amount'),
        ];

        $plans = Subscription::select('plan_name')->distinct()->pluck('plan_name');

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    private function filteredQuery(Request $request)
    {
        $query = Subscription::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name',     'like', "%{$search}%")
                    ->orWhere('last_name',    'like', "%{$search}%")
                    ->orWhere('full_name',    'like', "%{$search}%")
                    ->orWhere('email',        'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'expired') {
                $query->where('status', 'paid')->where('expiry_date', '<', Carbon::now());
            } elseif ($request->status === 'active') {
                $query->where('status', 'paid')->where('expiry_date', '>=', Carbon::now());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('plan') && $request->plan !== 'all') {
            $query->where('plan_name', $request->plan);
        }

        return $query;
    }

    public function export(Request $request)
    {
        $subscriptions = $this->filteredQuery($request)->get();
        $filename      = 'subscriptions_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($subscriptions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'User Name', 'Email', 'Mobile', 'Plan', 'Amount',
                'Start Date', 'Expiry Date', 'Status',
                'Razorpay Subscription ID', 'Razorpay Payment ID',
            ]);

            foreach ($subscriptions as $sub) {
                $isExpired     = $sub->status === 'paid' && Carbon::parse($sub->expiry_date)->isPast();
                $derivedStatus = $sub->status === 'paid'
                    ? ($isExpired ? 'Expired' : 'Active')
                    : ucwords(str_replace('_', ' ', $sub->status));

                fputcsv($handle, [
                    $sub->user->full_name
                        ?? trim(($sub->user->first_name ?? '') . ' ' . ($sub->user->last_name ?? ''))
                        ?: 'Unknown User',
                    $sub->user->email         ?? 'N/A',
                    $sub->user->mobile_number ?? 'N/A',
                    str_replace('_', ' ', $sub->plan_name),
                    $sub->amount,
                    Carbon::parse($sub->start_date)->format('Y-m-d'),
                    Carbon::parse($sub->expiry_date)->format('Y-m-d'),
                    $derivedStatus,
                    $sub->razorpay_subscription_id ?? 'N/A',
                    $sub->razorpay_payment_id      ?? 'N/A',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Subscription $subscription)
    {
        $subscription->load('user');

        $history = Subscription::where('user_id', $subscription->user_id)
            ->orderByDesc('start_date')->get();

        $isExpired = $subscription->status === 'paid'
            && Carbon::parse($subscription->expiry_date)->isPast();

        if ($subscription->status === 'cancelled') {
            $derivedStatus = 'cancelled';
        } elseif ($subscription->status === 'halted') {
            $derivedStatus = 'halted';
        } elseif ($subscription->status === 'paused') {
            $derivedStatus = 'paused';
        } elseif ($subscription->status === 'payment_failed') {
            $derivedStatus = 'payment_failed';
        } elseif ($subscription->status === 'refund_initiated') {
            $derivedStatus = 'refund_initiated';
        } elseif ($subscription->status === 'refunded') {
            $derivedStatus = 'refunded';
        } elseif ($subscription->status === 'completed') {
            $derivedStatus = 'completed';
        } elseif ($subscription->status === 'paid') {
            $derivedStatus = $isExpired ? 'expired' : 'active';
        } else {
            $derivedStatus = 'active';
        }

        return response()->json([
            'success'      => true,
            'subscription' => [
                'id'                       => $subscription->id,
                'plan_name'                => $subscription->plan_name,
                'plan_label'               => ucwords(str_replace('_', ' ', $subscription->plan_name)),
                'amount'                   => $subscription->amount,
                'start_date'               => Carbon::parse($subscription->start_date)->format('d M Y'),
                'expiry_date'              => Carbon::parse($subscription->expiry_date)->format('d M Y'),
                'days_left'                => $isExpired ? 0 : max(0, now()->diffInDays(
                    Carbon::parse($subscription->expiry_date),
                    false
                )),
                'status'                   => $derivedStatus,
                'razorpay_subscription_id' => $subscription->razorpay_subscription_id,
                'razorpay_payment_id'      => $subscription->razorpay_payment_id,
                'razorpay_signature'       => $subscription->razorpay_signature,
                'created_at'               => $subscription->created_at ? $subscription->created_at->format('d M Y, h:i A') : null,
            ],
            'user' => [
                'id'     => $subscription->user->id    ?? null,
                'name'   => $subscription->user->full_name
                    ?? trim(($subscription->user->first_name ?? '') . ' ' . ($subscription->user->last_name ?? ''))
                    ?: 'Unknown User',
                'email'  => $subscription->user->email         ?? null,
                'mobile' => $subscription->user->mobile_number ?? null,
            ],
            'history' => $history->map(function ($h) use ($subscription) {
                return [
                    'id'          => $h->id,
                    'plan_label'  => ucwords(str_replace('_', ' ', $h->plan_name)),
                    'amount'      => $h->amount,
                    'start_date'  => Carbon::parse($h->start_date)->format('d M Y'),
                    'expiry_date' => Carbon::parse($h->expiry_date)->format('d M Y'),
                    'status'      => $h->status,
                    'is_current'  => $h->id === $subscription->id,
                ];
            }),
        ]);
    }

    // ── Cancel ──
    public function cancel(Subscription $subscription)
    {
        if ($subscription->status === 'cancelled') {
            return response()->json(['success' => false, 'message' => 'Already cancelled.'], 422);
        }
        if (!$subscription->razorpay_subscription_id) {
            return response()->json(['success' => false, 'message' => 'No Razorpay subscription ID found.'], 422);
        }

        try {
            $this->getApi()->subscription
                ->fetch($subscription->razorpay_subscription_id)
                ->cancel(['cancel_at_cycle_end' => 0]);
        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            Log::warning('Razorpay cancel failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Razorpay rejected: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Razorpay cancel error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not reach Razorpay. Try again.'], 502);
        }

        $subscription->update(['status' => 'cancelled']);
        $subscription->user->update(['is_premium' => 0]);

        return response()->json(['success' => true, 'message' => 'Subscription cancelled successfully.']);
    }

    // ── Pause ──
    public function pause(Subscription $subscription)
    {
        if ($subscription->status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Only active subscriptions can be paused.'
            ], 422);
        }

        try {
            $this->getApi()->subscription
                ->fetch($subscription->razorpay_subscription_id)
                ->pause(['pause_at' => 'now']);
        } catch (\Exception $e) {
            Log::error('Razorpay pause error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not pause on Razorpay. Try again.'
            ], 502);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pause request sent. Status will update automatically.'
        ]);
    }

    // ── Resume ──
    public function resume(Subscription $subscription)
    {
        if ($subscription->status !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => 'Only paused subscriptions can be resumed.'
            ], 422);
        }

        try {
            // Fetch latest state from Razorpay first
            $rzpSub = $this->getApi()
                ->subscription
                ->fetch($subscription->razorpay_subscription_id);

            if ($rzpSub->status !== 'paused') {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription is not in paused state on Razorpay.'
                ], 422);
            }

            // Resume subscription
            $this->getApi()
                ->subscription
                ->fetch($subscription->razorpay_subscription_id)
                ->resume(['resume_at' => 'now']);
        } catch (\Exception $e) {
            Log::error('Razorpay resume error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not resume on Razorpay. Try again.'
            ], 502);
        }

        // ✅ IMPORTANT: don't fully trust this as final state
        // Razorpay will send "subscription.resumed" webhook

        $subscription->update([
            'status' => 'resuming' // temporary safe state
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resume request sent. Status will update automatically.'
        ]);
    }

    // ── Refund ──
    // ── Refund ──
    public function refund(Subscription $subscription)
    {
        if (in_array($subscription->status, ['refunded', 'refund_initiated'])) {
            return response()->json(['success' => false, 'message' => 'Refund already initiated.'], 422);
        }

        $paymentId = $subscription->razorpay_payment_id;

        // Fallback: no payment_id saved locally, but we have a subscription_id — recover
        // the payment_id from Razorpay's own invoice history instead of failing outright.
        if (!$paymentId) {
            if (!$subscription->razorpay_subscription_id) {
                return response()->json(['success' => false, 'message' => 'No payment ID or subscription ID found for refund.'], 422);
            }

            try {
                $invoices = $this->getApi()->invoice->all([
                    'subscription_id' => $subscription->razorpay_subscription_id,
                ]);
            } catch (\Exception $e) {
                Log::error('Fetch invoices error: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Could not fetch payment info from Razorpay.'], 502);
            }

            $paidInvoice = collect($invoices->items)
                ->filter(fn ($inv) => $inv->status === 'paid' && !empty($inv->payment_id))
                ->sortByDesc('created_at')
                ->first();

            if (!$paidInvoice) {
                return response()->json(['success' => false, 'message' => 'No captured payment found for this subscription — nothing was actually debited.'], 422);
            }

            $paymentId = $paidInvoice->payment_id;
        }

        // Verify the payment was actually captured (Case B guard) before attempting a refund
        try {
            $payment = $this->getApi()->payment->fetch($paymentId);
        } catch (\Exception $e) {
            Log::error('Fetch payment error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not find this payment on Razorpay.'], 422);
        }

        if (!$payment->captured) {
            return response()->json(['success' => false, 'message' => 'This payment was never captured — nothing was actually debited, so there is nothing to refund.'], 422);
        }

        if ($payment->status === 'refunded') {
            // Razorpay already refunded it (e.g. race with a previous attempt) — just sync locally.
            $subscription->update(['razorpay_payment_id' => $paymentId, 'status' => 'refunded']);
            $subscription->user->update(['is_premium' => 0]);
            return response()->json(['success' => false, 'message' => 'This payment was already refunded on Razorpay. Local record has been synced.'], 422);
        }

        try {
            // Use Razorpay's own recorded amount (already in paise) rather than trusting the local
            // $subscription->amount, in case they've ever drifted apart.
            $payment->refund(['amount' => $payment->amount]);
        } catch (\Exception $e) {
            Log::error('Razorpay refund error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not process refund. Try again.'], 502);
        }

        // Stop future auto-charges if the subscription is still live on Razorpay's side
        if ($subscription->status === 'paid' && $subscription->razorpay_subscription_id) {
            try {
                $this->getApi()->subscription
                    ->fetch($subscription->razorpay_subscription_id)
                    ->cancel(['cancel_at_cycle_end' => 0]);
            } catch (\Razorpay\Api\Errors\BadRequestError $e) {
                Log::warning('Refund->cancel rejected (already inactive): ' . $e->getMessage());
            } catch (\Exception $e) {
                // Refund already succeeded — don't fail the whole request over this. Log for manual follow-up.
                Log::error('Refund->cancel error (refund itself succeeded): ' . $e->getMessage());
            }
        }

        // Backfill payment_id (in case it was just recovered above) and mark pending confirmation.
        // Status stays 'refund_initiated' here on purpose — your refund.processed webhook is what
        // flips it to 'refunded' once Razorpay/the bank actually confirms completion.
        $subscription->update([
            'razorpay_payment_id' => $paymentId,
            'status'              => 'refund_initiated',
        ]);
        $subscription->user->update(['is_premium' => 0]);

        return response()->json(['success' => true, 'message' => 'Refund initiated. Status will update automatically once Razorpay confirms.']);
    }

    // ── Complete (manual admin only) ──
    // ── Complete (manual admin only) ──
    public function complete(Subscription $subscription)
    {
        if ($subscription->status !== 'paid') {
            return response()->json(['success' => false, 'message' => 'Only active subscriptions can be marked complete.'], 422);
        }

        if ($subscription->razorpay_subscription_id) {
            try {
                $this->getApi()->subscription
                    ->fetch($subscription->razorpay_subscription_id)
                    ->cancel(['cancel_at_cycle_end' => 0]);
            } catch (\Razorpay\Api\Errors\BadRequestError $e) {
                // Razorpay already considers it inactive/cancelled/completed — safe to proceed locally.
                Log::warning('Razorpay complete->cancel rejected (treating as already inactive): ' . $e->getMessage());
            } catch (\Exception $e) {
                Log::error('Razorpay complete->cancel error: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Could not reach Razorpay. Try again.'], 502);
            }
        }

        $subscription->update(['status' => 'completed']);
        // $subscription->user->update(['is_premium' => 0]);

        return response()->json(['success' => true, 'message' => 'Subscription marked as completed.']);
    }

    // ── Delete ──
    public function destroy(Subscription $subscription)
    {
        // Cancel on Razorpay if still active
        if ($subscription->razorpay_subscription_id) {
            try {
                $rzpSub = $this->getApi()->subscription
                    ->fetch($subscription->razorpay_subscription_id);

                // Only cancel if still active on Razorpay
                if (in_array($rzpSub->status, ['created', 'authenticated', 'active'])) {
                    $rzpSub->cancel(['cancel_at_cycle_end' => 0]);
                }
            } catch (\Razorpay\Api\Errors\BadRequestError $e) {
                // Already cancelled/completed on Razorpay — safe to proceed
                Log::warning('Destroy->cancel rejected (already inactive): ' . $e->getMessage());
            } catch (\Exception $e) {
                Log::error('Destroy->cancel error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Could not cancel on Razorpay. Delete aborted.'
                ], 502);
            }
        }

        // Revoke user access
        if ($subscription->user) {
            $subscription->user->update([
                'is_premium'          => 0,
                'current_plan'        => null,
                'subscription_expiry' => null,
            ]);
        }

        // Delete DB record
        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled on Razorpay and record deleted.'
        ]);
    }

    // ── Recover ──
    public function recover(Request $request)
    {
        $paymentId = trim($request->payment_id);

        if (!$paymentId) {
            return response()->json(['success' => false, 'message' => 'Payment ID is required.'], 422);
        }

        try {
            $payment = $this->getApi()->payment->fetch($paymentId);
        } catch (\Exception $e) {
            Log::error('Recovery fetch error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment ID not found on Razorpay.'], 422);
        }

        if ($payment->status !== 'captured') {
            return response()->json([
                'success' => false,
                'message' => 'Payment status is "' . $payment->status . '" — only captured payments can be recovered.'
            ], 422);
        }

        $existing = Subscription::where('razorpay_payment_id', $paymentId)->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'This payment is already recorded in the database.'], 422);
        }

        $subId  = isset($payment->subscription_id) ? $payment->subscription_id : null;
        $plan   = isset($payment->notes['plan'])    ? $payment->notes['plan']   : null;
        $userId = isset($payment->notes['user_id']) ? $payment->notes['user_id'] : null;

        if (!$userId && $subId) {
            $existingSub = Subscription::where('razorpay_subscription_id', $subId)->latest()->first();
            if ($existingSub) {
                $userId = $existingSub->user_id;
                $plan   = $plan ?? $existingSub->plan_name;
            }
        }

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Could not find user for this payment. Check Razorpay notes.'], 422);
        }

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Could not determine plan for this payment. Check Razorpay notes.'], 422);
        }

        $expiry = $this->getExpiry($plan);

        Subscription::create([
            'user_id'                  => $userId,
            'plan_name'                => $plan,
            'amount'                   => $payment->amount / 100,
            'razorpay_subscription_id' => $subId,
            'razorpay_payment_id'      => $paymentId,
            'start_date'               => Carbon::now(),
            'expiry_date'              => $expiry,
            'status'                   => 'paid',
        ]);

        \App\Models\User::where('id', $userId)->update([
            'is_premium'          => 1,
            'current_plan'        => $plan,
            'subscription_expiry' => $expiry,
        ]);

        Log::info('Manual payment recovery done', [
            'payment_id' => $paymentId,
            'user_id'    => $userId,
            'plan'       => $plan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment recovered. Subscription activated for user #' . $userId . '.'
        ]);
    }

    // ── getExpiry (same as frontend) ──
    private function getExpiry($plan, $from = null)
    {
        $from = $from ?? Carbon::now();

        if ($plan == '7_day')   return $from->copy()->addDays(7);
        if ($plan == '1_month') return $from->copy()->addMonth();
        if ($plan == '6_month') return $from->copy()->addMonths(6);
        if ($plan == '1_year')  return $from->copy()->addYear();

        return $from->copy()->addMonth();
    }

    // ── Direct Refund by Payment ID (no DB row needed) ──
    public function directRefund(Request $request)
    {
        $paymentId = trim($request->payment_id);

        if (!$paymentId) {
            return response()->json(['success' => false, 'message' => 'Payment ID is required.'], 422);
        }

        // Fetch from Razorpay
        try {
            $payment = $this->getApi()->payment->fetch($paymentId);
        } catch (\Exception $e) {
            Log::error('Direct refund fetch error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment ID not found on Razorpay.'], 422);
        }

        // Must be captured
        if (!$payment->captured) {
            return response()->json([
                'success' => false,
                'message' => 'Payment was never captured — nothing to refund.'
            ], 422);
        }

        // Already refunded
        if ($payment->status === 'refunded') {
            // Sync DB if row exists
            $sub = Subscription::where('razorpay_payment_id', $paymentId)->latest()->first();
            if ($sub) {
                $sub->update(['status' => 'refunded']);
                $sub->user->update(['is_premium' => 0]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Payment already refunded on Razorpay.'
            ], 422);
        }

        // Process refund
        try {
            $payment->refund(['amount' => $payment->amount]);
        } catch (\Exception $e) {
            Log::error('Direct refund error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not process refund. Try again.'], 502);
        }

        // Update DB if row exists
        $sub = Subscription::where('razorpay_payment_id', $paymentId)->latest()->first();
        if ($sub) {
            $sub->update(['status' => 'refund_initiated']);
            $sub->user->update(['is_premium' => 0]);
        }

        // Cancel subscription on Razorpay if still active
        if ($sub && $sub->razorpay_subscription_id) {
            try {
                $this->getApi()->subscription
                    ->fetch($sub->razorpay_subscription_id)
                    ->cancel(['cancel_at_cycle_end' => 0]);
            } catch (\Exception $e) {
                Log::warning('Direct refund->cancel error: ' . $e->getMessage());
            }
        }

        Log::info('Direct refund processed', ['payment_id' => $paymentId]);

        return response()->json([
            'success' => true,
            'message' => 'Refund initiated successfully.'
        ]);
    }
}

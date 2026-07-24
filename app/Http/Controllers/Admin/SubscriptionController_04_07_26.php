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
    /**
     * Display a listing of subscriptions with search, status & plan filters.
     */
    public function index(Request $request)
    {
        $subscriptions = $this->filteredQuery($request)->paginate(15)->withQueryString();

        // Summary stats for the header cards
        $stats = [
            'total'     => Subscription::count(),
            'active'    => Subscription::where('status', 'paid')
                                ->where('expiry_date', '>=', Carbon::now())
                                ->count(),
            'expired'   => Subscription::where('status', 'paid')
                                ->where('expiry_date', '<', Carbon::now())
                                ->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'revenue'   => Subscription::where('status', 'paid')->sum('amount'),
        ];

        // Distinct plan names for the filter dropdown
        $plans = Subscription::select('plan_name')->distinct()->pluck('plan_name');

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    /**
     * Shared query builder for search/status/plan filters.
     * Used by both index() (paginated) and export() (full CSV) so the
     * two stay in sync — exporting always matches whatever is on screen.
     */
    private function filteredQuery(Request $request)
    {
        $query = Subscription::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'expired') {
                $query->where('status', 'paid')
                      ->where('expiry_date', '<', Carbon::now());
            } elseif ($request->status === 'active') {
                $query->where('status', 'paid')
                      ->where('expiry_date', '>=', Carbon::now());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('plan') && $request->plan !== 'all') {
            $query->where('plan_name', $request->plan);
        }

        return $query;
    }

    /**
     * Export the currently filtered subscription list as a CSV download.
     * Streams the response so it stays memory-safe even with large tables.
     */
    public function export(Request $request)
    {
        $subscriptions = $this->filteredQuery($request)->get();

        $filename = 'subscriptions_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($subscriptions) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'User Name', 'Email', 'Mobile', 'Plan', 'Amount',
                'Start Date', 'Expiry Date', 'Status', 'Razorpay Subscription ID',
                'Razorpay Payment ID',
            ]);

            foreach ($subscriptions as $sub) {
                $isExpired = $sub->status === 'paid' && Carbon::parse($sub->expiry_date)->isPast();
                $derivedStatus = $sub->status === 'cancelled'
                    ? 'Cancelled'
                    : ($isExpired ? 'Expired' : 'Active');

                fputcsv($handle, [
                    $sub->user->full_name
                        ?? trim(($sub->user->first_name ?? '') . ' ' . ($sub->user->last_name ?? ''))
                        ?: 'Unknown User',
                    $sub->user->email ?? 'N/A',
                    $sub->user->mobile_number ?? 'N/A',
                    str_replace('_', ' ', $sub->plan_name),
                    $sub->amount,
                    Carbon::parse($sub->start_date)->format('Y-m-d'),
                    Carbon::parse($sub->expiry_date)->format('Y-m-d'),
                    $derivedStatus,
                    $sub->razorpay_subscription_id ?? 'N/A',
                    $sub->razorpay_payment_id ?? 'N/A',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Return a single subscription's details as JSON, for the modal.
     * Includes the user's full subscription/renewal history for context.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load('user');

        $history = Subscription::where('user_id', $subscription->user_id)
            ->orderByDesc('start_date')
            ->get();

        $isExpired = $subscription->status === 'paid'
            && Carbon::parse($subscription->expiry_date)->isPast();

        $derivedStatus = $subscription->status === 'cancelled'
            ? 'cancelled'
            : ($isExpired ? 'expired' : 'active');

        return response()->json([
            'success' => true,
            'subscription' => [
                'id'                       => $subscription->id,
                'plan_name'                => $subscription->plan_name,
                'plan_label'               => ucwords(str_replace('_', ' ', $subscription->plan_name)),
                'amount'                   => $subscription->amount,
                'start_date'               => Carbon::parse($subscription->start_date)->format('d M Y'),
                'expiry_date'              => Carbon::parse($subscription->expiry_date)->format('d M Y'),
                'days_left'                => $isExpired ? 0 : max(0, now()->diffInDays(Carbon::parse($subscription->expiry_date), false)),
                'status'                   => $derivedStatus,
                'razorpay_subscription_id' => $subscription->razorpay_subscription_id,
                'razorpay_payment_id'      => $subscription->razorpay_payment_id,
                'razorpay_signature'       => $subscription->razorpay_signature,
                'created_at'               => $subscription->created_at ? $subscription->created_at->format('d M Y, h:i A') : null,
            ],
            'user' => [
                'id'     => $subscription->user->id ?? null,
                'name'   => $subscription->user->full_name
                                ?? trim(($subscription->user->first_name ?? '') . ' ' . ($subscription->user->last_name ?? ''))
                                ?: 'Unknown User',
                'email'  => $subscription->user->email ?? null,
                'mobile' => $subscription->user->mobile_number ?? null,
                'img'    => $subscription->user->img ?? null,
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

    /**
     * Get a configured Razorpay API client — same pattern used in
     * RazorpayController, kept local here so this controller doesn't
     * need to depend on that one.
     */
    private function getApi()
    {
        return new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    /**
     * Cancel a subscription — both on Razorpay (stops autopay immediately)
     * and locally (status = cancelled). The Razorpay call happens FIRST;
     * local status is only updated if Razorpay confirms the cancellation.
     * This keeps the DB and Razorpay's records from drifting apart if the
     * API call fails for any reason.
     */
    public function cancel(Subscription $subscription)
    {
        if ($subscription->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This subscription is already cancelled.',
            ], 422);
        }

        if (!$subscription->razorpay_subscription_id) {
            return response()->json([
                'success' => false,
                'message' => 'No Razorpay subscription ID on this record — cannot cancel on Razorpay.',
            ], 422);
        }

        try {
            // cancel_at_cycle_end = 0 → stop billing immediately, not at period end
            $this->getApi()->subscription->fetch($subscription->razorpay_subscription_id)
                ->cancel(['cancel_at_cycle_end' => 0]);

        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            // Razorpay throws this if the subscription is already cancelled/completed
            // on their side, or the ID is invalid/doesn't exist.
            Log::warning('Razorpay cancel failed (bad request): ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'razorpay_subscription_id' => $subscription->razorpay_subscription_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Razorpay rejected the cancellation: ' . $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Razorpay cancel failed: ' . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'razorpay_subscription_id' => $subscription->razorpay_subscription_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not reach Razorpay to cancel this subscription. Please try again.',
            ], 502);
        }

        // Only reaches here if the Razorpay call succeeded
        $subscription->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled on Razorpay and marked as cancelled.',
        ]);
    }

    /**
     * Delete a subscription record.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription record deleted.',
        ]);
    }
}
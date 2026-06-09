<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = $request->query('user_id');

            $query = Subscription::query();

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $subscriptions = $query
                ->with('user:id,full_name,email,mobile_number')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($sub) {
                    return [
                        'id'                       => $sub->id,
                        'user_id'                  => $sub->user_id,
                        'user'                     => $sub->user,
                        'plan_name'                => $sub->plan_name,
                        'plan_label'               => $this->getPlanLabel($sub->plan_name),
                        'amount'                   => $sub->amount,
                        'razorpay_order_id'        => $sub->razorpay_order_id,
                        'razorpay_payment_id'      => $sub->razorpay_payment_id,
                        'razorpay_subscription_id' => $sub->razorpay_subscription_id,
                        'start_date'               => $sub->start_date,
                        'expiry_date'              => $sub->expiry_date,
                        'status'                   => $sub->status,
                        'is_active'                => \Carbon\Carbon::parse($sub->expiry_date)->isFuture(),
                        'days_remaining'           => max(0, \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($sub->expiry_date), false)),
                        'created_at'               => $sub->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'count'   => $subscriptions->count(),
                'data'    => $subscriptions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getPlanLabel($planName)
    {
        $labels = [
            '1_month' => '1 Month',
            '3_month' => '3 Months',
            '6_month' => '6 Months',
            '1_year'  => '1 Year',
            '2_year'  => '2 Years',
            '3_year'  => '3 Years',
        ];
        return $labels[$planName] ?? ucfirst(str_replace('_', ' ', $planName ?? 'N/A'));
    }
}

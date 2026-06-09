<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_name',
        'amount',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'start_date',
        'expiry_date',
        'status',
        'razorpay_subscription_id'
    ];
}
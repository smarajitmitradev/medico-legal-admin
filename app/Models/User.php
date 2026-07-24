<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'full_name',
        'first_name',
        'last_name',
        'mobile_number',
        'country_code',
        'email',
        'otp',
        'otp_expires_at',
        'refresh_token',
        'device_id',
        'fcm_token',
        'temp_fcm_token',
        'user_type',
        'is_profile_complete',
        'is_premium',
        'premium_expiry_date',
        'img',
        'takeover_token',
        'takeover_expires_at',
        'refresh_token_expires_at',
        'platform',
        'app_id',
        'delete_reason',
        'subscription_expiry',
        'current_plan',
    ];

    /**
     * Hidden fields
     */
    protected $hidden = [
        'otp',
        'refresh_token',
        'remember_token',
        'password',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'otp_expires_at' => 'datetime',
        'premium_expiry_date' => 'datetime',
        'takeover_expires_at' => 'datetime',
        'refresh_token_expires_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_profile_complete' => 'boolean',
        'is_premium' => 'boolean',
    ];

    /**
     * JWT Identifier
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * JWT Custom Claims
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
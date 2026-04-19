<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

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
        // ✅ NEW (FCM)
        'fcm_token',
        'temp_fcm_token', // optional
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
    ];

    /**
     * Hidden fields
     */
    protected $hidden = [
        'otp',
        'refresh_token',
        'remember_token',
        'password',
        'takeover_expires_at' => 'datetime',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'otp_expires_at' => 'datetime',
        'premium_expiry_date' => 'datetime',
        'is_profile_complete' => 'boolean',
        'is_premium' => 'boolean',
        'refresh_token_expires_at' => 'datetime',
    ];

    /**
     * JWT Identifier
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // uses default 'id'
    }

    /**
     * JWT Custom Claims
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}

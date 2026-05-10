<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'otp',
        'profile_pic',
        'otp_expires_at',
        'otp_locked_until'
    ];

    protected $hidden = [
        'password',
        'otp' // 🔒 hide OTP from serialization
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'otp_locked_until' => 'datetime',
    ];

    /**
     * ✅ Check if OTP is expired
     */
    public function isOtpExpired()
    {
        return !$this->otp_expires_at || now()->gt($this->otp_expires_at);
    }

    /**
     * ✅ Check if account is locked due to too many attempts
     */
    public function isOtpLocked()
    {
        return $this->otp_locked_until && now()->lt($this->otp_locked_until);
    }

    /**
     * ✅ Clear OTP after successful verification
     */
    public function clearOtp()
    {
        $this->update([
            'otp' => null,
            'otp_expires_at' => null,
            'otp_locked_until' => null
        ]);
    }

    /**
     * ✅ Set OTP (hashed + expiry)
     */
    public function setOtp($otp)
    {
        $this->update([
            'otp' => bcrypt($otp),
            'otp_expires_at' => now()->addMinutes(5),
            'otp_locked_until' => null
        ]);
    }
}
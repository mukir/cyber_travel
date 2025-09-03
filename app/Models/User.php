<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Mail\VerifyEmailMail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class, // cast role to enum
        ];
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        Mail::to($this->email)->send(new VerifyEmailMail($this));
    }

    // ---- Role Helpers ----
    public function is_admin(): bool
    {
        return $this->role === UserRole::Admin || $this->role === 'admin';
    }

    public function is_client(): bool
    {
        return $this->role === UserRole::Client || $this->role === 'client';
    }

    public function is_staff(): bool
    {
        return $this->role === UserRole::Staff || $this->role === 'staff';
    }
}

<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Mail\VerifyEmailMail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable 
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
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
            'is_active'         => 'boolean',
        ];
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

    public function is_reception(): bool
    {
        return $this->role === UserRole::Reception || $this->role === 'reception';
    }
}

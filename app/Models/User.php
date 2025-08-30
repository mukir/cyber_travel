<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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

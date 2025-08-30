<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'dob', 'phone', 'email', 'gender', 'id_no',
        'county', 'next_of_kin', 'service_package', 'status',
        'application_date', 'interview_date', 'travel_date', 'sales_rep_id',
    ];

    public function salesRep(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

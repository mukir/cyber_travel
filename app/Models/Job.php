<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    // Use a dedicated table name to avoid clashing with Laravel queue 'jobs' table
    protected $table = 'service_jobs';

    protected $fillable = [
        'name', 'slug', 'description', 'base_price', 'active',
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(JobPackage::class);
    }
}

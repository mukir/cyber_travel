<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If a domain-specific 'jobs' table exists (identified by having a 'slug' column),
        // rename it to 'service_jobs' to avoid clashing with Laravel's queue 'jobs' table.
        if (Schema::hasTable('jobs') && Schema::hasColumn('jobs', 'slug') && !Schema::hasTable('service_jobs')) {
            Schema::rename('jobs', 'service_jobs');
        }
        // Foreign keys generally survive a table rename in MySQL. If your DB engine
        // requires manual FK updates, you can adjust them in a follow-up migration.
    }

    public function down(): void
    {
        // Only rename back if there is no conflicting queue 'jobs' table
        if (Schema::hasTable('service_jobs') && !Schema::hasTable('jobs')) {
            Schema::rename('service_jobs', 'jobs');
        }
    }
};


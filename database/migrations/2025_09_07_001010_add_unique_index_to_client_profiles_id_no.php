<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            // Add unique index if not present
            try {
                $table->unique('id_no', 'client_profiles_id_no_unique');
            } catch (\Throwable $e) {
                // index may already exist
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            try {
                $table->dropUnique('client_profiles_id_no_unique');
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};


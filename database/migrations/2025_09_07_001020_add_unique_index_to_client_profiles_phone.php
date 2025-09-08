<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            try {
                $table->unique('phone', 'client_profiles_phone_unique');
            } catch (\Throwable $e) {
                // Index may already exist; ignore
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            try {
                $table->dropUnique('client_profiles_phone_unique');
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};


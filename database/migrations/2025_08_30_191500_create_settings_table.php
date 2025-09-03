<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default currency to KES if not present
        try {
            DB::table('settings')->updateOrInsert(
                ['key' => 'default_currency'],
                ['value' => 'KES', 'created_at' => now(), 'updated_at' => now()]
            );
        } catch (\Throwable $e) {
            // ignore if table not ready in some environments
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};


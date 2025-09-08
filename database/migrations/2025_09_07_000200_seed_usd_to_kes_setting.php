<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::table('settings')->updateOrInsert(
                ['key' => 'currency.usd_to_kes'],
                ['value' => '135', 'created_at' => now(), 'updated_at' => now()]
            );
        } catch (\Throwable $e) {
            // Table may not exist yet in some environments; ignore
        }
    }

    public function down(): void
    {
        try {
            DB::table('settings')->where('key', 'currency.usd_to_kes')->delete();
        } catch (\Throwable $e) {
            // ignore
        }
    }
};


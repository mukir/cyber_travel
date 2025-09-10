<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            // Gulf
            ['name' => 'Saudi Arabia', 'code' => 'SA', 'region' => 'gulf'],
            ['name' => 'United Arab Emirates', 'code' => 'AE', 'region' => 'gulf'],
            ['name' => 'Qatar', 'code' => 'QA', 'region' => 'gulf'],
            ['name' => 'Bahrain', 'code' => 'BH', 'region' => 'gulf'],
            ['name' => 'Kuwait', 'code' => 'KW', 'region' => 'gulf'],
            ['name' => 'Oman', 'code' => 'OM', 'region' => 'gulf'],
            // Americas
            ['name' => 'United States', 'code' => 'US', 'region' => 'americas'],
            ['name' => 'Canada', 'code' => 'CA', 'region' => 'americas'],
            ['name' => 'Brazil', 'code' => 'BR', 'region' => 'americas'],
            // Europe (subset)
            ['name' => 'Poland', 'code' => 'PL', 'region' => 'europe'],
            ['name' => 'Germany', 'code' => 'DE', 'region' => 'europe'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'region' => 'europe'],
            ['name' => 'Italy', 'code' => 'IT', 'region' => 'europe'],
            ['name' => 'France', 'code' => 'FR', 'region' => 'europe'],
        ];
        foreach ($rows as $r) {
            try {
                DB::table('countries')->updateOrInsert(
                    ['name' => $r['name']],
                    [
                        'code' => $r['code'],
                        'region' => $r['region'],
                        'active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    public function down(): void
    {
        // keep seeded data
    }
};


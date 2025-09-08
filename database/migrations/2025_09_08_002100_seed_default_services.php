<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            [
                'slug' => 'international-travel-planning',
                'title' => 'International Travel Planning',
                'summary' => 'We take the stress out of global travel. From flights and visas to hotels and itineraries, Cyber Travel Agency plans your international trips with precision and care.',
                'position' => 1,
                'active' => true,
            ],
            [
                'slug' => 'overseas-education-support',
                'title' => 'Overseas Education Support',
                'summary' => 'We guide students through every step of studying abroad—from choosing the right course and institution to securing admission and a student visa.',
                'position' => 2,
                'active' => true,
            ],
            [
                'slug' => 'global-job-placement-assistance',
                'title' => 'Global Job Placement Assistance',
                'summary' => 'We assist Drivers, House helps, Security guards, construction workers, Cooks, Chefs, Gardeners, supervisors, cashiers, shop attendants, Saloonist, Cleaners, Warehouse attendants, Graphic Designers to realise their dreams.',
                'position' => 3,
                'active' => true,
            ],
            [
                'slug' => 'visa-and-documentation-services',
                'title' => 'Visa & Documentation Services',
                'summary' => 'We handle your visa applications and travel documentation with accuracy and speed—no stress, no delays.',
                'position' => 4,
                'active' => true,
            ],
            [
                'slug' => 'pre-departure-and-relocation-support',
                'title' => 'Pre-Departure & Relocation Support',
                'summary' => 'We help you prepare for life abroad with travel tips, housing, packing lists, currency exchange advice, and local insights.',
                'position' => 5,
                'active' => true,
            ],
            [
                'slug' => 'adventure-tour',
                'title' => 'Adventure Tour',
                'summary' => 'Fuel your wanderlust with thrilling adventures around the world. From mountain hikes to desert safaris—we turn adrenaline into unforgettable memories.',
                'position' => 6,
                'active' => true,
            ],
        ];

        foreach ($rows as $r) {
            try {
                DB::table('services')->updateOrInsert(
                    ['slug' => $r['slug']],
                    [
                        'title' => $r['title'],
                        'summary' => $r['summary'],
                        'active' => (bool)$r['active'],
                        'position' => (int)$r['position'],
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
        // Keep seeded services; no-op
    }
};


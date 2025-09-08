<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categories = [
            ['slug' => 'travel',            'name' => 'Travel',            'description' => 'International travel planning and trips',              'active' => true],
            ['slug' => 'education',         'name' => 'Education',         'description' => 'Study abroad and education support',                 'active' => true],
            ['slug' => 'jobs',              'name' => 'Jobs',              'description' => 'Global job placement and work abroad',               'active' => true],
            ['slug' => 'visa-documentation','name' => 'Visa & Documentation','description' => 'Visa applications and documentation services',    'active' => true],
            ['slug' => 'relocation',        'name' => 'Relocation',        'description' => 'Pre-departure and relocation assistance',           'active' => true],
            ['slug' => 'adventure',         'name' => 'Adventure',         'description' => 'Adventure tours and experiences',                   'active' => true],
        ];

        foreach ($categories as $c) {
            try {
                DB::table('service_categories')->updateOrInsert(
                    ['slug' => $c['slug']],
                    [
                        'name' => $c['name'],
                        'description' => $c['description'],
                        'active' => (bool)$c['active'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } catch (\Throwable $e) {
                // ignore seeding errors
            }
        }

        // Map default services to categories if both exist
        $map = [
            'international-travel-planning'      => 'travel',
            'overseas-education-support'         => 'education',
            'global-job-placement-assistance'    => 'jobs',
            'visa-and-documentation-services'    => 'visa-documentation',
            'pre-departure-and-relocation-support'=> 'relocation',
            'adventure-tour'                     => 'adventure',
        ];

        try {
            $cats = DB::table('service_categories')->pluck('id', 'slug');
            $jobs = DB::table('service_jobs')->pluck('id', 'slug');
            foreach ($map as $serviceSlug => $catSlug) {
                $jobId = $jobs[$serviceSlug] ?? null;
                $catId = $cats[$catSlug] ?? null;
                if ($jobId && $catId) {
                    $exists = DB::table('service_category_job')
                        ->where('service_category_id', $catId)
                        ->where('job_id', $jobId)
                        ->exists();
                    if (!$exists) {
                        DB::table('service_category_job')->insert([
                            'service_category_id' => $catId,
                            'job_id' => $jobId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore mapping errors
        }
    }

    public function down(): void
    {
        // Keep categories; no destructive downgrade
    }
};


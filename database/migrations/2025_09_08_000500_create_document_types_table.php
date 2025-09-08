<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // machine key e.g., passport, good_conduct
            $table->string('name');          // human readable name
            $table->text('description')->nullable();
            $table->boolean('required')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Seed common defaults
        try {
            \Illuminate\Support\Facades\DB::table('document_types')->insert([
                ['key' => 'passport', 'name' => 'Passport', 'required' => true, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'good_conduct', 'name' => 'Certificate of Good Conduct', 'required' => true, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'cv', 'name' => 'Curriculum Vitae (CV)', 'required' => true, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'photo', 'name' => 'Passport Photo', 'required' => true, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};

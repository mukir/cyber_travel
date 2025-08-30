<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Reference domain jobs table
            $table->foreignId('job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->foreignId('job_package_id')->constrained('job_packages')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->date('start_date')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('KES');
            $table->string('status')->default('pending');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

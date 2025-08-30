<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total_amount');
            $table->string('payment_status')->default('unpaid')->after('amount_paid'); // unpaid, deposit, installments, full
            $table->string('invoice_number')->nullable()->after('payment_status');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('method'); // mpesa, paypal
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->string('reference')->nullable(); // checkout/order id or receipt
            $table->string('receipt_number')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'payment_status', 'invoice_number']);
        });
    }
};


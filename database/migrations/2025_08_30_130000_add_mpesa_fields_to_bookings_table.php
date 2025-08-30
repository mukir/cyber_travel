<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('mpesa_checkout_id')->nullable()->after('status');
            $table->string('mpesa_merchant_request_id')->nullable()->after('mpesa_checkout_id');
            $table->string('mpesa_receipt')->nullable()->after('mpesa_merchant_request_id');
            $table->timestamp('paid_at')->nullable()->after('mpesa_receipt');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['mpesa_checkout_id', 'mpesa_merchant_request_id', 'mpesa_receipt', 'paid_at']);
        });
    }
};


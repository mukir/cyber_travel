<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('commissions', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('type');
            }
            if (!Schema::hasColumn('commissions', 'payout_month')) {
                $table->string('payout_month')->nullable()->after('paid_at'); // e.g., 2025-08
            }
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'payout_month')) {
                $table->dropColumn('payout_month');
            }
            if (Schema::hasColumn('commissions', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }
};


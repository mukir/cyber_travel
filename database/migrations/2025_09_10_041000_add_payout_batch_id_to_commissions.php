<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('commissions', 'payout_batch_id')) {
                $table->foreignId('payout_batch_id')->nullable()->after('payout_month')->constrained('payout_batches')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'payout_batch_id')) {
                $table->dropConstrainedForeignId('payout_batch_id');
            }
        });
    }
};


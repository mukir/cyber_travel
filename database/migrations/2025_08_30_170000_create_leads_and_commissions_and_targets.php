<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_rep_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('stage')->default('new'); // new, contacted, qualified, won, lost
            $table->string('status')->default('open'); // open, closed
            $table->date('next_follow_up')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('sales_rep_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->date('next_follow_up')->nullable();
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('rate', 5, 2)->default(0); // percentage
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('type')->default('milestone'); // milestone, referral
            $table->timestamps();
        });

        Schema::create('sales_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->string('period')->default('monthly'); // monthly, quarterly
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('target_amount', 10, 2);
            $table->timestamps();
        });

        // Referral tracking
        if (!Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('referral_code')->nullable()->unique()->after('remember_token');
            });
        }
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'referred_by_id')) {
                $table->foreignId('referred_by_id')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
            }
            if (!Schema::hasColumn('bookings', 'referral_code')) {
                $table->string('referral_code')->nullable()->after('referred_by_id');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('leads');
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'referred_by_id')) {
                $table->dropConstrainedForeignId('referred_by_id');
            }
            if (Schema::hasColumn('bookings', 'referral_code')) {
                $table->dropColumn('referral_code');
            }
        });
        if (Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['referral_code']);
                $table->dropColumn('referral_code');
            });
        }
    }
};


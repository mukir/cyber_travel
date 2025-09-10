<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('service_jobs', 'country')) {
                $table->string('country')->nullable()->after('description');
            }
            if (!Schema::hasColumn('service_jobs', 'region')) {
                $table->string('region')->nullable()->after('country'); // europe, gulf, americas, other
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('service_jobs', 'region')) {
                $table->dropColumn('region');
            }
            if (Schema::hasColumn('service_jobs', 'country')) {
                $table->dropColumn('country');
            }
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('client_documents') && !Schema::hasColumn('client_documents', 'validated')) {
            Schema::table('client_documents', function (Blueprint $table) {
                $table->boolean('validated')->default(false)->after('path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('client_documents') && Schema::hasColumn('client_documents', 'validated')) {
            Schema::table('client_documents', function (Blueprint $table) {
                $table->dropColumn('validated');
            });
        }
    }
};


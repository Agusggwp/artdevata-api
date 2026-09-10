<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'company_name')) {
                $table->string('company_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('clients', 'website')) {
                $table->string('website')->nullable()->after('address');
            }
            if (!Schema::hasColumn('clients', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('website');
            }
            if (!Schema::hasColumn('clients', 'lead_id')) {
                $table->foreignId('lead_id')->nullable()->after('id')->constrained('leads')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'lead_id')) {
                $table->dropForeign(['lead_id']);
                $table->dropColumn('lead_id');
            }
            if (Schema::hasColumn('clients', 'company_name')) {
                $table->dropColumn('company_name');
            }
            if (Schema::hasColumn('clients', 'website')) {
                $table->dropColumn('website');
            }
            if (Schema::hasColumn('clients', 'tax_id')) {
                $table->dropColumn('tax_id');
            }
        });
    }
};

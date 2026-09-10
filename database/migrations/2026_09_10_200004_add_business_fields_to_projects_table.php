<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'project_number')) {
                $table->string('project_number')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('projects', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('project_number')->constrained('clients')->nullOnDelete();
            }
            if (!Schema::hasColumn('projects', 'quotation_id')) {
                $table->foreignId('quotation_id')->nullable()->after('client_id')->constrained('quotations')->nullOnDelete();
            }
            if (!Schema::hasColumn('projects', 'priority')) {
                $table->string('priority')->default('normal')->after('status');
            }
            if (!Schema::hasColumn('projects', 'deadline')) {
                $table->date('deadline')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('projects', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('deadline');
            }
            if (!Schema::hasColumn('projects', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('budget')->constrained('admins')->nullOnDelete();
            }
            if (!Schema::hasColumn('projects', 'notes')) {
                $table->text('notes')->nullable()->after('progress');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            }
            if (Schema::hasColumn('projects', 'quotation_id')) {
                $table->dropForeign(['quotation_id']);
                $table->dropColumn('quotation_id');
            }
            if (Schema::hasColumn('projects', 'client_id')) {
                $table->dropForeign(['client_id']);
                $table->dropColumn('client_id');
            }
            $columnsToDrop = array_filter(['project_number', 'priority', 'deadline', 'completed_at', 'notes'], function ($col) {
                return Schema::hasColumn('projects', $col);
            });
            if (!empty($columnsToDrop)) {
                $table->dropColumn(array_values($columnsToDrop));
            }
        });
    }
};

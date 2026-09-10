<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'role_id')) {
                $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->onDelete('set null');
            }
            if (!Schema::hasColumn('admins', 'status')) {
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('email');
            }
            if (!Schema::hasColumn('admins', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            if (!Schema::hasColumn('admins', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('password');
            }
            if (!Schema::hasColumn('admins', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('admins', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('last_login_ip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
            $cols = array_filter(['status', 'remember_token', 'last_login_at', 'last_login_ip', 'last_activity_at'], function($c) {
                return Schema::hasColumn('admins', $c);
            });
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum to varchar(50) so any status (planning, in_progress, review, completed, on_hold, etc.) can be stored without data truncation
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'planning'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('ongoing', 'completed', 'pending') NOT NULL DEFAULT 'pending'");
        }
    }
};

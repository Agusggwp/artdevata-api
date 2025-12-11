<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->string('role')->nullable(); // Developer, Designer, Manager, dll
            $table->timestamps();
            $table->unique(['project_id', 'admin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_admin');
    }
};
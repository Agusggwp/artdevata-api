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
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('cascade');
            $table->string('email');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['success', 'failed', 'logout']);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['admin_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};

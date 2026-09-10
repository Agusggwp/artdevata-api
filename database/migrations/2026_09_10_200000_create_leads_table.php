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
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('source', ['website', 'whatsapp', 'instagram', 'tiktok', 'facebook', 'referral', 'walk_in', 'other'])->default('website');
            $table->string('service_interest')->nullable();
            $table->decimal('estimated_budget', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'negotiation', 'won', 'lost'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->dateTime('next_follow_up_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

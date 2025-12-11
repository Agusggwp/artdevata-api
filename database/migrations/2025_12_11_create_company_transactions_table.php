<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->enum('type', ['credit', 'debit']); // credit = tambah saldo, debit = kurangi saldo
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->decimal('balance_after', 15, 2)->nullable(); // optional snapshot
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_transactions');
    }
};
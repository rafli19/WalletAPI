<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Buat file baru: database/migrations/2024_01_01_000001_create_transactions_table.php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['topup', 'transfer_in', 'transfer_out']);
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('balance_before');
            $table->unsignedBigInteger('balance_after');
            $table->string('description')->nullable();
            $table->string('reference_id')->unique();
            $table->timestamps();

            $table->index('user_id');
            $table->index('reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
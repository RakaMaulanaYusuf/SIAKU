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
        // 1. Tabel Kode Akun
        Schema::create('account_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('auxiliary_table')->nullable();
            $table->enum('balance_position', ['DEBET', 'KREDIT']);
            $table->enum('report_position', ['NERACA', 'LABA RUGI']);
            $table->decimal('initial_balance_debit', 15, 2)->default(0);
            $table->decimal('initial_balance_credit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_codes');
    }
};

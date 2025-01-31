<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->date('date');
            $table->string('transaction_proof');
            $table->text('description');
            $table->string('account_id');
            $table->string('helper_id')->nullable();
            $table->decimal('debit', 15, 2)->nullable();  // Dibuat nullable
            $table->decimal('credit', 15, 2)->nullable(); // Dibuat nullable
            $table->timestamps();
            
            $table->foreign('account_id')->references('account_id')->on('kode_akun')->onDelete('cascade');
            $table->foreign('helper_id')->references('helper_id')->on('kode_bantu')->onDelete('set null');
            $table->index(['date', 'transaction_proof']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_umum');
    }
};
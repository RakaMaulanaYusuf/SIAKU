<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kode_bantu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('ID Perusahaan');
            $table->string('helper_id')
                  ->comment('Kode Bantu');
            $table->string('name')
                  ->comment('Nama');
            $table->enum('status', ['PIUTANG', 'HUTANG'])
                  ->default('PIUTANG')
                  ->comment('Status');
            $table->decimal('balance', 15, 2)
                  ->default(0)
                  ->nullable()
                  ->comment('Saldo Awal');
            $table->timestamps();

            $table->index('helper_id');
            $table->index('name');
            $table->unique(['company_id', 'helper_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kode_bantu');
    }
};
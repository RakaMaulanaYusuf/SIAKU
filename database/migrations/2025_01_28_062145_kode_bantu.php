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
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->nullable(false)
                  ->comment('ID Perusahaan');
            $table->foreignId('company_period_id')
                  ->constrained('company_period')
                  ->onDelete('cascade')
                  ->nullable(false)
                  ->comment('ID Period');
            $table->string('helper_id')
                  ->comment('Kode Bantu');
            $table->string('name')
                  ->comment('Nama');
            $table->enum('status', ['PIUTANG', 'HUTANG'])
                  ->default('PIUTANG')
                  ->comment('Status');
            $table->decimal('balance', 15, 2)
                  ->default(0)
                  ->comment('Saldo Awal');
            $table->timestamps();

            $table->index('helper_id');
            $table->index('name');
            // Making the combination unique for company-period-helper_id
            $table->unique(['company_id', 'company_period_id', 'helper_id'], 'unique_helper_per_company_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kode_bantu');
    }
};
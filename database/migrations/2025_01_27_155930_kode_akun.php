<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kode_akun', function (Blueprint $table) {
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
            $table->string('account_id')
                  ->comment('Kode Akun');
            $table->string('name')
                  ->comment('Nama Akun');
            $table->string('helper_table')
                  ->nullable()
                  ->comment('Tabel Bantuan');
            $table->enum('balance_type', ['DEBIT', 'CREDIT'])
                  ->default('DEBIT')
                  ->comment('Pos Saldo');
            $table->enum('report_type', ['NERACA', 'LABARUGI'])
                  ->default('NERACA')
                  ->comment('Pos Laporan');
            $table->decimal('debit', 15, 2)
                  ->default(0)
                  ->nullable()
                  ->comment('Saldo Awal Debet');
            $table->decimal('credit', 15, 2)
                  ->default(0)
                  ->nullable()
                  ->comment('Saldo Awal Kredit');
            $table->timestamps();

            $table->index('account_id');
            $table->index('name');
            // Making the combination unique for company-period-account_id
            $table->unique(['company_id', 'company_period_id', 'account_id'], 'unique_account_per_company_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kode_akun');
    }
};
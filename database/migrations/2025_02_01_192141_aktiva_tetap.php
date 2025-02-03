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
        Schema::create('aktiva_tetap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('account_id');
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('account_id')->references('account_id')->on('kode_akun');
            $table->unique(['company_id', 'account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktiva_tetap');
    }
};

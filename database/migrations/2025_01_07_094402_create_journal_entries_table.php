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
        // 3. Tabel Jurnal Umum
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('reference_number');
            $table->text('description');
            $table->string('auxiliary_code')->nullable();
            $table->timestamps();
        });

        Schema::create('journal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
            $table->string('account_code');
            $table->string('auxiliary_code')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('journal_details');
    }
};

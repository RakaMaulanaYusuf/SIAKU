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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'staff'])->default('staff');
            $table->string('profile_photo')->nullable();
            $table->foreignId('active_company_id')->nullable()->constrained('companies')->onDelete('set null');
            // $table->foreignId('assigned_company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->foreignId('company_period_id')->nullable()->constrained('company_period')->onDelete('set null');
            // $table->foreignId('assigned_company_period_id')->nullable()->constrained('company_period')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
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
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('tax_identifier')->nullable();
            $table->string('status')->default('active')->index(); // active, suspended, archived
            $table->string('default_currency', 3)->default('EGP');
            $table->string('default_timezone')->default('Africa/Cairo');
            $table->string('default_locale', 5)->default('ar');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

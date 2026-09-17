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
        Schema::create('branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('timezone')->default('Africa/Cairo');
            $table->string('status')->default('active')->index(); // active, temporarily_closed, archived
            $table->string('phone')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->default('EG');
            $table->string('tax_registration_number')->nullable();
            $table->json('business_hours')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'code']);
            $table->index(['organization_id', 'status']);
            $table->index(['brand_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};

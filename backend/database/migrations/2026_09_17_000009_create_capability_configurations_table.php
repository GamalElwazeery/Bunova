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
        Schema::create('capability_configurations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->string('capability_key'); // e.g. gaming, wifi, venue.tables
            $table->string('status')->default('enabled'); // enabled, needs_configuration, disabled
            $table->json('config')->nullable();
            $table->string('version')->default('1.0.0');
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->text('disable_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['organization_id', 'branch_id', 'status']);
            $table->index(['organization_id', 'capability_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capability_configurations');
    }
};

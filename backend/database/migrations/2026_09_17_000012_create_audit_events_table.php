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
        Schema::create('audit_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->index();
            $table->uuid('branch_id')->nullable()->index();
            $table->timestamp('occurred_at', 6)->index();
            $table->string('actor_id', 64)->index();
            $table->string('actor_type', 32)->index(); // staff, user, device, system
            $table->uuid('device_id')->nullable()->index();
            $table->string('action_key', 128)->index();
            $table->string('target_type', 128)->index();
            $table->string('target_id', 64)->index();
            $table->string('correlation_id', 64)->index();
            $table->text('reason')->nullable();
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at', 6);

            // Composite indexes for common query patterns
            $table->index(['organization_id', 'occurred_at'], 'audit_org_occurred_idx');
            $table->index(['organization_id', 'target_type', 'target_id'], 'audit_org_target_idx');
            $table->index(['organization_id', 'action_key'], 'audit_org_action_idx');

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->cascadeOnDelete();

            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_events');
    }
};

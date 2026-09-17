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
        Schema::create('outbox_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_name')->index();
            $table->string('aggregate_type')->index();
            $table->string('aggregate_id')->index();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->string('correlation_id')->index();
            $table->string('actor_id')->index();
            $table->string('actor_type')->default('staff'); // staff, user, device, system
            $table->json('payload');
            $table->string('status')->default('pending')->index(); // pending, processing, dispatched, failed
            $table->integer('attempts')->default(0);
            $table->timestamp('dispatched_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['organization_id', 'branch_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbox_events');
    }
};

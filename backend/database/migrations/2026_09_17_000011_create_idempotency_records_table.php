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
        Schema::create('idempotency_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('scope', 255)->index();
            $table->string('idempotency_key', 255);
            $table->char('request_hash', 64);
            $table->string('status', 32)->default('in_progress'); // in_progress, completed, failed
            $table->string('resource_type', 128)->nullable();
            $table->uuid('resource_id')->nullable();
            $table->integer('response_code')->nullable();
            $table->json('response_headers')->nullable();
            $table->json('response_body')->nullable();
            $table->timestamp('locked_at', 6);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['scope', 'idempotency_key'], 'idempotency_scope_key_unique');
            $table->index(['expires_at'], 'idempotency_expires_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idempotency_records');
    }
};

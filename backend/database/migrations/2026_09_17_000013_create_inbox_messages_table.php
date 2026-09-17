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
        Schema::create('inbox_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('consumer_name', 128);
            $table->string('message_id', 128);
            $table->string('event_name', 128)->index();
            $table->string('status', 32)->default('processing')->index(); // processing, processed, failed
            $table->char('payload_hash', 64)->nullable();
            $table->integer('attempts')->default(1);
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable()->index();
            $table->timestamps();

            // Strict unique constraint: a message can only be processed once per consumer
            $table->unique(['consumer_name', 'message_id'], 'inbox_consumer_message_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbox_messages');
    }
};

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
        Schema::create('registered_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('device_code'); // e.g. REG-01, KDS-01, TAB-01
            $table->string('name'); // e.g. "Main Cashier Register 1"
            $table->string('device_type'); // pos_register, handheld_waiter, kds_kitchen, kds_bar, customer_facing_display, manager_tablet
            $table->string('status')->default('active'); // active, suspended, revoked
            $table->string('api_key_prefix', 16)->nullable()->index();
            $table->string('token_hash', 64)->nullable()->unique();
            $table->json('capabilities')->nullable(); // ['cash_drawer', 'receipt_printing', 'barcode_scanner', 'card_terminal', 'offline_orders']
            $table->json('hardware_metadata')->nullable(); // platform, os_version, app_version, model
            $table->json('settings')->nullable(); // peripheral mapping, sound, display layout
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('revocation_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['organization_id', 'branch_id', 'device_code']);
            $table->index(['organization_id', 'branch_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registered_devices');
    }
};

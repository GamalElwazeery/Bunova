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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('organization_id')->nullable()->after('id')->constrained('organizations')->cascadeOnDelete();
            $table->string('user_type')->default('tenant_user')->index()->after('email'); // tenant_user, platform_operator
            $table->string('status')->default('active')->index()->after('user_type');
            $table->string('phone')->nullable()->after('name');
            $table->json('settings')->nullable()->after('password');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'user_type', 'status', 'phone', 'settings', 'deleted_at']);
        });
    }
};

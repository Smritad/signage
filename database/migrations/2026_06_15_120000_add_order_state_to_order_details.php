<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Admin-managed order lifecycle state:
            // null = active, 'cancelled_by_user', 'refunded', 'closed'
            $table->string('order_state', 30)->nullable()->after('status');
            $table->timestamp('order_state_at')->nullable()->after('order_state');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['order_state', 'order_state_at']);
        });
    }
};

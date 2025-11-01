<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('escrow_status', ['pending','holding','released','refunded'])
                  ->default('pending')->after('status');
            $table->foreignId('pickup_id')->nullable()->after('escrow_status')
                  ->constrained('campus_pickups')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['pickup_id']);
            $table->dropColumn(['escrow_status', 'pickup_id']);
        });
    }
};

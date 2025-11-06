<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (!Schema::hasColumn('orders', 'pickup_point_id')) {
                $t->foreignId('pickup_point_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'pickup_scheduled_at')) {
                $t->dateTime('pickup_scheduled_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'pickup_note')) {
                $t->string('pickup_note')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            // Nếu đã có FK thì drop an toàn
            if (Schema::hasColumn('orders', 'pickup_point_id')) {
                // tên cột là 'pickup_point_id', drop cả FK + column
                $t->dropConstrainedForeignId('pickup_point_id');
            }
            if (Schema::hasColumn('orders', 'pickup_scheduled_at')) {
                $t->dropColumn('pickup_scheduled_at');
            }
            if (Schema::hasColumn('orders', 'pickup_note')) {
                $t->dropColumn('pickup_note');
            }
        });
    }
};

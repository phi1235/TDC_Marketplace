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
        Schema::table('listings', function (Blueprint $table) {
            // Số lần đã nhân bản từ tin gốc này
            $table->unsignedInteger('duplicate_count')->default(0)->after('rejected_by');
            // ID của tin gốc (nếu tin này là bản sao)
            $table->foreignId('duplicate_source_id')->nullable()->constrained('listings')->onDelete('set null')->after('duplicate_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['duplicate_source_id']);
            $table->dropColumn(['duplicate_count', 'duplicate_source_id']);
        });
    }
};

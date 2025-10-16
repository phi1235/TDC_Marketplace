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
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('reporter_id')->after('id');
            $table->morphs('reportable'); // reportable_id, reportable_type
            $table->string('reason')->after('reportable_type');
            $table->text('description')->nullable()->after('reason');
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending')->after('description');
            $table->text('admin_notes')->nullable()->after('status');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('admin_notes');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            
            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['reporter_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['reporter_id', 'reportable_id', 'reportable_type', 'reason', 'description', 'status', 'admin_notes', 'reviewed_by', 'reviewed_at']);
        });
    }
};

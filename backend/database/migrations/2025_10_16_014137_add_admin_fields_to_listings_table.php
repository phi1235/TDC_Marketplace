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
            $table->text('admin_notes')->nullable()->after('views_count');
            $table->enum('rejection_reason', ['inappropriate_content', 'spam', 'duplicate', 'policy_violation', 'other'])->nullable()->after('admin_notes');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_by');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn([
                'admin_notes',
                'rejection_reason',
                'approved_at',
                'approved_by',
                'rejected_at',
                'rejected_by'
            ]);
        });
    }
};

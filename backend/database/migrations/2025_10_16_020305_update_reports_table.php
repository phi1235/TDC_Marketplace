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
            if (!Schema::hasColumn('reports', 'reporter_id')) {
                $table->unsignedBigInteger('reporter_id')->after('id');
            }
            if (!Schema::hasColumn('reports', 'reportable_id') && !Schema::hasColumn('reports', 'reportable_type')) {
                $table->morphs('reportable');
            } else {
                if (!Schema::hasColumn('reports', 'reportable_id')) {
                    $table->unsignedBigInteger('reportable_id')->after('reporter_id');
                }
                if (!Schema::hasColumn('reports', 'reportable_type')) {
                    $table->string('reportable_type')->after('reportable_id');
                }
            }
            if (!Schema::hasColumn('reports', 'reason')) {
                $table->string('reason')->after('reportable_type');
            }
            if (!Schema::hasColumn('reports', 'description')) {
                $table->text('description')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('reports', 'status')) {
                $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending')->after('description');
            }
            if (!Schema::hasColumn('reports', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('reports', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('reports', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'reporter_id')) $table->dropColumn('reporter_id');
            if (Schema::hasColumn('reports', 'reportable_id')) $table->dropColumn('reportable_id');
            if (Schema::hasColumn('reports', 'reportable_type')) $table->dropColumn('reportable_type');
            if (Schema::hasColumn('reports', 'reason')) $table->dropColumn('reason');
            if (Schema::hasColumn('reports', 'description')) $table->dropColumn('description');
            if (Schema::hasColumn('reports', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('reports', 'admin_notes')) $table->dropColumn('admin_notes');
            if (Schema::hasColumn('reports', 'reviewed_by')) $table->dropColumn('reviewed_by');
            if (Schema::hasColumn('reports', 'reviewed_at')) $table->dropColumn('reviewed_at');
        });
    }
};

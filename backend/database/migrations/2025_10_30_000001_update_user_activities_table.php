<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('user_activities', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->index()->after('id');
            }
            if (!Schema::hasColumn('user_activities', 'event_name')) {
                $table->string('event_name', 100)->index()->after('user_id');
            }
            if (!Schema::hasColumn('user_activities', 'metadata')) {
                $table->json('metadata')->nullable()->after('event_name');
            }
            if (!Schema::hasColumn('user_activities', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('metadata');
            }
            if (!Schema::hasColumn('user_activities', 'user_agent')) {
                $table->string('user_agent', 255)->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            if (Schema::hasColumn('user_activities', 'user_agent')) $table->dropColumn('user_agent');
            if (Schema::hasColumn('user_activities', 'ip_address')) $table->dropColumn('ip_address');
            if (Schema::hasColumn('user_activities', 'metadata')) $table->dropColumn('metadata');
            if (Schema::hasColumn('user_activities', 'event_name')) $table->dropColumn('event_name');
            if (Schema::hasColumn('user_activities', 'user_id')) $table->dropColumn('user_id');
        });
    }
};



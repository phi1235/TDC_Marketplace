<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'ai_enabled')) {
                $table->boolean('ai_enabled')->default(false)->after('is_support');
            }
        });

        DB::table('conversations')
            ->where('is_support', true)
            ->update(['ai_enabled' => true]);
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'ai_enabled')) {
                $table->dropColumn('ai_enabled');
            }
        });
    }
};

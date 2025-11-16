<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('disputes', function (Blueprint $table) {
            if (!Schema::hasColumn('disputes', 'listing_id')) {
                $table->foreignId('listing_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }

            if (!Schema::hasColumn('disputes', 'against_user_id')) {
                $table->foreignId('against_user_id')->nullable()->after('opener_id')->constrained('users')->onDelete('cascade');
            }
        });
    }

    public function down(): void {
        Schema::table('disputes', function (Blueprint $table) {
            if (Schema::hasColumn('disputes', 'listing_id')) {
                $table->dropForeign(['listing_id']);
                $table->dropColumn('listing_id');
            }

            if (Schema::hasColumn('disputes', 'against_user_id')) {
                $table->dropForeign(['against_user_id']);
                $table->dropColumn('against_user_id');
            }
        });
    }
};

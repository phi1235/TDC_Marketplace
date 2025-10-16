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
        Schema::table('listing_views', function (Blueprint $table) {
            $table->unsignedBigInteger('listing_id')->after('id');
            $table->unsignedBigInteger('user_id')->nullable()->after('listing_id');
            $table->string('ip_address')->after('user_id');
            $table->text('user_agent')->nullable()->after('ip_address');
            
            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_views', function (Blueprint $table) {
            $table->dropForeign(['listing_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['listing_id', 'user_id', 'ip_address', 'user_agent']);
        });
    }
};

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
        Schema::table('listing_images', function (Blueprint $table) {
            $table->unsignedBigInteger('listing_id')->after('id');
            $table->string('image_path')->after('listing_id');
            $table->boolean('is_primary')->default(false)->after('image_path');
            $table->integer('sort_order')->default(0)->after('is_primary');
            
            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_images', function (Blueprint $table) {
            $table->dropForeign(['listing_id']);
            $table->dropColumn(['listing_id', 'image_path', 'is_primary', 'sort_order']);
        });
    }
};

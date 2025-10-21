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
            $table->string('original_name')->nullable()->after('image_path');
            $table->integer('file_size')->nullable()->after('original_name');
            $table->string('mime_type')->nullable()->after('file_size');
            $table->integer('width')->nullable()->after('mime_type');
            $table->integer('height')->nullable()->after('width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_images', function (Blueprint $table) {
            $table->dropColumn(['original_name', 'file_size', 'mime_type', 'width', 'height']);
        });
    }
};
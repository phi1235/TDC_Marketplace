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
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên ngành học
            $table->string('slug')->unique(); // URL-friendly slug
            $table->text('description')->nullable(); // Mô tả ngành
            $table->string('icon')->nullable(); // Icon emoji hoặc class
            $table->boolean('is_active')->default(true); // Trạng thái active
            $table->integer('display_order')->default(0); // Thứ tự hiển thị
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};

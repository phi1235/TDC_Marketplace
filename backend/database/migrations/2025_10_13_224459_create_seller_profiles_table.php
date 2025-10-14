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
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->onDelete('cascade');
            $table->string('student_id', 20)->nullable();
            $table->string('student_id_image', 255)->nullable();
            $table->boolean('verified_student')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_ratings')->default(0);
            $table->integer('total_sales')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0.00);
            $table->text('bio')->nullable();
            $table->string('academic_year', 20)->nullable();
            $table->string('major', 100)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
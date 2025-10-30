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
    Schema::table('users', function (Blueprint $table) {
        $table->decimal('rating', 3, 2)->default(0)->after('avatar');
        $table->unsignedInteger('total_ratings')->default(0)->after('rating');
        $table->unsignedInteger('total_sales')->default(0)->after('total_ratings');
        $table->decimal('total_revenue', 12, 2)->default(0)->after('total_sales');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['rating', 'total_ratings', 'total_sales', 'total_revenue']);
    });
}

};

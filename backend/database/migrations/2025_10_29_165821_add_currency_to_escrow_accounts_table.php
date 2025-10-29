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
    Schema::table('escrow_accounts', function (Blueprint $table) {
        $table->string('currency', 10)->default('VND')->after('amount');
    });
}

public function down(): void
{
    Schema::table('escrow_accounts', function (Blueprint $table) {
        $table->dropColumn('currency');
    });
}

};

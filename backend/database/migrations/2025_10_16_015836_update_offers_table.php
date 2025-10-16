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
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedBigInteger('listing_id')->after('id');
            $table->unsignedBigInteger('buyer_id')->after('listing_id');
            $table->decimal('offer_price', 10, 2)->after('buyer_id');
            $table->text('message')->nullable()->after('offer_price');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending')->after('message');
            $table->timestamp('expires_at')->nullable()->after('status');
            
            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['listing_id']);
            $table->dropForeign(['buyer_id']);
            $table->dropColumn(['listing_id', 'buyer_id', 'offer_price', 'message', 'status', 'expires_at']);
        });
    }
};

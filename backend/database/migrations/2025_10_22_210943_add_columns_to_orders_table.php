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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->unique()->after('id');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade')->after('order_number');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade')->after('buyer_id');
            $table->foreignId('listing_id')->nullable()->constrained()->onDelete('set null')->after('seller_id');
            $table->foreignId('offer_id')->nullable()->constrained()->onDelete('set null')->after('listing_id');
            
            $table->string('product_title')->after('offer_id');
            $table->decimal('product_price', 10, 2)->after('product_title');
            $table->integer('quantity')->default(1)->after('product_price');
            $table->decimal('total_amount', 10, 2)->after('quantity');
            $table->string('currency', 3)->default('VND')->after('total_amount');
            
            $table->enum('status', ['pending', 'confirmed', 'paid', 'shipped', 'delivered', 'completed', 'cancelled'])->default('pending')->after('currency');
            
            $table->foreignId('pickup_point_id')->nullable()->constrained('campus_pickups')->onDelete('set null')->after('status');
            $table->enum('delivery_method', ['campus_pickup', 'home_delivery', 'meet_in_person'])->default('campus_pickup')->after('pickup_point_id');
            $table->text('delivery_address')->nullable()->after('delivery_method');
            $table->text('delivery_notes')->nullable()->after('delivery_address');
            
            $table->timestamp('paid_at')->nullable()->after('delivery_notes');
            $table->timestamp('confirmed_at')->nullable()->after('paid_at');
            $table->timestamp('shipped_at')->nullable()->after('confirmed_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->timestamp('completed_at')->nullable()->after('delivered_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            
            $table->text('notes')->nullable()->after('cancelled_at');
            $table->text('admin_notes')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
            $table->dropForeign(['seller_id']);
            $table->dropForeign(['listing_id']);
            $table->dropForeign(['offer_id']);
            $table->dropForeign(['pickup_point_id']);
            
            $table->dropColumn([
                'order_number', 'buyer_id', 'seller_id', 'listing_id', 'offer_id',
                'product_title', 'product_price', 'quantity', 'total_amount', 'currency',
                'status', 'pickup_point_id', 'delivery_method', 'delivery_address', 'delivery_notes',
                'paid_at', 'confirmed_at', 'shipped_at', 'delivered_at', 'completed_at', 'cancelled_at',
                'notes', 'admin_notes'
            ]);
        });
    }
};

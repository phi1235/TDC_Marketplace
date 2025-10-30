<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            // ai gửi đánh giá
            $table->foreignId('from_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // đánh giá cho ai
            $table->foreignId('to_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // đơn hàng nào
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            // số sao 1-5
            $table->unsignedTinyInteger('score')->default(5);

            // nhận xét text
            $table->text('comment')->nullable();

            $table->timestamps();

            // 1 người chỉ được rate một lần cho 1 order
            $table->unique(['order_id', 'from_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};

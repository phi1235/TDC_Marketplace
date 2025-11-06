<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('disputes', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('opener_id')->nullable()->after('order_id')->constrained('users');
            $table->string('reason')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['open','under_review','resolved','rejected'])->default('open');
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
        });
    }

    public function down(): void {
        Schema::table('disputes', function (Blueprint $table) {
            $table->dropForeign(['order_id', 'opener_id']);
            $table->dropColumn(['order_id','opener_id','reason','description','status','resolution','resolved_at']);
        });
    }
};

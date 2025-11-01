<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_errors', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->string('level', 20)->default('error')->index();
            $table->integer('status')->nullable()->index();
            $table->string('message', 512);
            $table->text('trace')->nullable();
            $table->string('route', 255)->nullable()->index();
            $table->string('method', 10)->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('request_id', 64)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_errors');
    }
};



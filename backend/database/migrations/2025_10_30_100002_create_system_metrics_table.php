<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_metrics', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->string('endpoint', 255)->index();
            $table->string('method', 10)->index();
            $table->integer('status')->index();
            $table->integer('duration_ms')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_metrics');
    }
};



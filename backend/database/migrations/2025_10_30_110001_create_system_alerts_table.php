<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_alerts', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->string('rule', 100); // error_rate|p95|service_down
            $table->string('level', 20)->default('warning');
            $table->string('message', 255);
            $table->json('context')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamp('resolved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_alerts');
    }
};



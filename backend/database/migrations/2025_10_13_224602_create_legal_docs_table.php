<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_docs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['terms', 'privacy', 'guidelines', 'cookie', 'refund'])->default('terms');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('version')->default('v1.0.0');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('type');
            $table->index('is_active');
            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_docs');
    }
};
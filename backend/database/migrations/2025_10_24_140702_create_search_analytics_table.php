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
        Schema::create('search_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->timestamp('timestamp');
            $table->decimal('elasticsearch_response_time', 8, 2);
            $table->integer('elasticsearch_result_count');
            $table->boolean('elasticsearch_success');
            $table->decimal('solr_response_time', 8, 2);
            $table->integer('solr_result_count');
            $table->boolean('solr_success');
            $table->enum('winner', ['elasticsearch', 'solr', 'tie']);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->index(['timestamp']);
            $table->index(['user_id']);
            $table->index(['winner']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_analytics');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('listing_pickup_point', function (Blueprint $t) {
            $t->id();
            $t->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $t->foreignId('pickup_point_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['listing_id','pickup_point_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('listing_pickup_point'); }
};


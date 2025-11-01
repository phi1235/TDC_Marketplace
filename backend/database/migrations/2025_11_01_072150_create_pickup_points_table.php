<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pickup_points', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('campus_code')->nullable();   // ví dụ: TDC-CNTT
            $t->string('address')->nullable();
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->json('opening_hours')->nullable();   // [{day:'Mon-Fri',time:'8:00-17:00'}]
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pickup_points'); }
};



<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_ingredient_pins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('pinned_order')->nullable();
            $table->timestamps();

            $table->unique(['user_id','ingredient_id']);
            $table->index(['user_id','pinned_order']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_ingredient_pins');
    }
};

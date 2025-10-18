<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->index();
            $table->unsignedInteger('pinned_order')->nullable()->index();
            $table->timestamp('pinned_at')->nullable()->index();
        });
    }
    public function down(): void {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn(['is_pinned','pinned_order','pinned_at']);
        });
    }
};


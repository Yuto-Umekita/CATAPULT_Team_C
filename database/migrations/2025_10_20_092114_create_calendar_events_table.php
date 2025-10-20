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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->string('item_name')->nullable();
            $table->enum('type', ['入庫', '出庫']);
            $table->integer('quantity')->nullable();
            $table->date('date');
            $table->enum('status', ['予定', '完了', '遅延'])->default('予定');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};

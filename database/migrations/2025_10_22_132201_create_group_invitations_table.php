<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('inviter_id')->constrained('users')->onDelete('cascade');
            $table->string('email'); // 招待先
            $table->string('token')->unique(); // 招待トークン
            $table->boolean('accepted')->default(false); // 承認状態
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_invitations');
    }
};

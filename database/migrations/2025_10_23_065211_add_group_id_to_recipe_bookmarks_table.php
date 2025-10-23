<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipe_bookmarks', function (Blueprint $table) {
            // 🔹 各ブックマークがどのグループに属するかを管理
            $table->unsignedBigInteger('group_id')->nullable()->after('user_id');

            // 🔹 外部キー制約（任意）
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('recipe_bookmarks', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};

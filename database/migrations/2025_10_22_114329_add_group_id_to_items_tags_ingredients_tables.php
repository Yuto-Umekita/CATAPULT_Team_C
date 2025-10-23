<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🧩 items テーブルに group_id を追加
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'group_id')) {
                $table->foreignId('group_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('groups')
                    ->onDelete('cascade');
            }
        });

        // 🏷 tags テーブルに group_id を追加
        Schema::table('tags', function (Blueprint $table) {
            if (!Schema::hasColumn('tags', 'group_id')) {
                $table->foreignId('group_id')
                    ->nullable()
                    ->after('item_id')
                    ->constrained('groups')
                    ->onDelete('cascade');
            }
        });

        // 🍳 ingredients テーブルに group_id を追加（←修正箇所）
        Schema::table('ingredients', function (Blueprint $table) {
            if (!Schema::hasColumn('ingredients', 'group_id')) {
                $table->foreignId('group_id')
                    ->nullable()
                    ->after('id') // user_id が存在しないため id の後に変更
                    ->constrained('groups')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admin_passwords', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_passwords', 'group_id')) {
                $table->unsignedBigInteger('group_id')->nullable()->after('id');
            }
        });

        // ✅ 既存レコードに仮の group_id を設定（例: 1）
        if (Schema::hasColumn('admin_passwords', 'group_id')) {
            DB::table('admin_passwords')->whereNull('group_id')->update(['group_id' => 1]);
        }

        // ✅ 外部キーは group_id の値が全て有効になってから追加
        Schema::table('admin_passwords', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('admin_passwords', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};




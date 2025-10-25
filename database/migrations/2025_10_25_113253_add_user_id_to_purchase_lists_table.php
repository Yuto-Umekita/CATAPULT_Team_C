<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_lists', function (Blueprint $table) {
            // ✅ まだ存在していなければ user_id カラムを追加
            if (!Schema::hasColumn('purchase_lists', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('group_id');
            }

            // （必要に応じて外部キーも）
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_lists', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};

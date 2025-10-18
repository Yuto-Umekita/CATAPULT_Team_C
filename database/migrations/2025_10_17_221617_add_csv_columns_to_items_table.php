<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('items', function (Blueprint $table) {
            // UUID（CSV識別用）を追加
            if (!Schema::hasColumn('items', 'item_id')) {
                $table->uuid('item_id')->unique()->after('id');
            }

            // 単位（unit）を quantity の後に追加（stock ではない）
            if (!Schema::hasColumn('items', 'unit')) {
                $table->string('unit', 32)->nullable()->after('quantity');
            }

            // 更新日時テキスト
            if (!Schema::hasColumn('items', 'updated_at_text')) {
                $table->string('updated_at_text')->nullable()->after('unit');
            }

            // 賞味期限
            if (!Schema::hasColumn('items', 'best_before')) {
                $table->date('best_before')->nullable()->after('updated_at_text');
            }

            // 消費期限
            if (!Schema::hasColumn('items', 'use_by')) {
                $table->date('use_by')->nullable()->after('best_before');
            }

            // 最終更新者
            if (!Schema::hasColumn('items', 'last_updated_by')) {
                $table->string('last_updated_by')->nullable()->after('use_by');
            }
        });
    }

    public function down(): void {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'item_id',
                'unit',
                'updated_at_text',
                'best_before',
                'use_by',
                'last_updated_by',
            ]);
        });
    }
};

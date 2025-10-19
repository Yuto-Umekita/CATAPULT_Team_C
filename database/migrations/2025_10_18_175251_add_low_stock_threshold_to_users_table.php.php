<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 🧮 デフォルトは3（従来と同じ判定）
            $table->unsignedInteger('low_stock_threshold')->default(3)->after('notify_system');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('low_stock_threshold');
        });
    }
};

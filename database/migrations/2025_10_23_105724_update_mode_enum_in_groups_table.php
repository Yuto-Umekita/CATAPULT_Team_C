<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 一時的に ENUM → VARCHAR に変更（制約解除）
        DB::statement("ALTER TABLE `groups` MODIFY COLUMN `mode` VARCHAR(20) NOT NULL DEFAULT 'household';");
    }

    public function down(): void
    {
        // 戻す場合
        DB::statement("ALTER TABLE `groups` MODIFY COLUMN `mode` ENUM('household', 'company') NOT NULL DEFAULT 'household';");
    }
};

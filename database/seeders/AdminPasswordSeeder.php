<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminPassword;

class AdminPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // すでに登録済みならスキップ
        if (AdminPassword::exists()) {
            return;
        }

        // 初回のみ自動登録
        $defaultPassword = 'admin1234';
        AdminPassword::create(['password' => $defaultPassword]);

        // コンソールに表示（php artisan db:seed時のみ）
        $this->command->info("✅ 初期管理者パスワードを登録しました: {$defaultPassword}");
    }
}

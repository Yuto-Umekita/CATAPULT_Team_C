<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_passwords', function (Blueprint $table) {
            $table->id();
            // パスワードをハッシュ化して保存する
            $table->string('password')->comment('共通管理者パスワードのハッシュ');
            $table->timestamps();
        });

        // 💡 初期値の設定
        // .envから初期パスワードを取得し、ハッシュ化してデータベースに挿入します。
        $initialPassword = env('ADMIN_ACCESS_PASSWORD', 'default_admin_password');
        
        DB::table('admin_passwords')->insert([
            'password' => Hash::make($initialPassword),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_passwords');
    }
};
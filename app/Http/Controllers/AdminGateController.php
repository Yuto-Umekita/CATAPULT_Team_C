<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminPassword;
use Illuminate\Support\Facades\Hash;

class AdminGateController extends Controller
{
    /**
     * 🔐 管理者パスワード入力フォームを表示
     */
    public function show()
    {
        // config/admin.php の値を読む。なければ '0000'
        $defaultPassword = config('admin.default_password', '0000');

        // ✅ 初回のみ：DBに管理パスワードが無ければ作成する
        if (AdminPassword::count() === 0) {
            AdminPassword::create(['password' => $defaultPassword]);
            session()->flash('first_time_info', "初回パスワード：{$defaultPassword}");
        }

        return view('admin.gate');
    }

    /**
     * ✅ パスワード認証処理
     */
    public function check(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $stored = AdminPassword::first();

        if ($stored && Hash::check($request->password, $stored->password)) {
            // 💡 ユーザーごとにゲート通過フラグを持つ
            session(['admin_authenticated_' . auth()->id() => true]);

            // セッション固定攻撃対策でIDを再発行
            session()->regenerate();

            // 任意: ログを残したいなら（監査用）
            \Log::info('Admin gate passed', [
                'user_id' => auth()->id(),
                'time'    => now(),
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', '管理者認証に成功しました。');
        }

        return back()
            ->withErrors(['password' => 'パスワードが正しくありません。'])
            ->withInput();
    }
}

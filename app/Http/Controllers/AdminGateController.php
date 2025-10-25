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
        // ✅ 現在選択中のグループを取得
        $groupId = session('selected_group_id');

        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        // ✅ グループ専用パスワードを取得
        $defaultPassword = config('admin.default_password', '0000');
        $adminPassword = AdminPassword::where('group_id', $groupId)->first();

        // ✅ 初回のみ：該当グループのパスワードが無ければ作成
        if (!$adminPassword) {
            AdminPassword::create([
                'group_id' => $groupId,
                'password' => $defaultPassword,
            ]);
            session()->flash('first_time_info', "初回パスワード：{$defaultPassword}");
        }

        return view('admin.gate');
    }

    /**
     * ✅ パスワード認証処理（グループ別）
     */
    public function check(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $groupId = session('selected_group_id');

        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        $stored = AdminPassword::where('group_id', $groupId)->first();

        if ($stored && Hash::check($request->password, $stored->password)) {
            // ✅ グループ単位で認証セッションを保持
            session(['admin_authenticated_group' => $groupId]);
            session()->regenerate();

            \Log::info('Admin gate passed', [
                'user_id'  => auth()->id(),
                'group_id' => $groupId,
                'time'     => now(),
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', '管理者認証に成功しました。');
        }

        return back()
            ->withErrors(['password' => 'パスワードが正しくありません。'])
            ->withInput();
    }
}

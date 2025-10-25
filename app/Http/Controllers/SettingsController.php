<?php

namespace App\Http\Controllers;

use App\Models\AdminPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * ⚙️ 設定画面を表示（グループ単位）
     */
    public function index()
    {
        $user = Auth::user();

        // ✅ グループ選択チェック
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        return view('settings.index', compact('user', 'groupId'));
    }

    /**
     * 💾 一般ユーザー設定を更新（通知・在庫しきい値など）
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // ✅ 入力検証
        $validated = $request->validate([
            'low_stock_threshold' => 'required|integer|min:1|max:50',
        ]);

        // ✅ 通知設定を更新
        $user->notify_low_stock = $request->boolean('notify_low_stock');
        $user->notify_recipe_updates = $request->boolean('notify_recipe_updates');
        $user->notify_system = $request->boolean('notify_system');
        $user->low_stock_threshold = $validated['low_stock_threshold'];
        $user->save();

        return redirect()->route('settings.index')->with('success', '設定を保存しました。');
    }

    /**
     * 🔐 管理者パスワードをグループごとに更新
     * ※ admin.access ミドルウェアで保護
     */
    public function updateAdminPassword(Request $request)
    {
        $user = Auth::user();

        // ✅ 管理者でない場合は拒否
        if (!$user->is_admin) {
            abort(403, '管理者権限が必要です。');
        }

        // ✅ グループ選択チェック
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        // ✅ 入力検証
        $validated = $request->validate([
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // ✅ グループ単位でパスワードを保存または更新
        AdminPassword::updateOrCreate(
            ['group_id' => $groupId],
            ['password' => $validated['admin_password']]
        );

        return redirect()->back()->with('success', 'このグループの管理者パスワードを更新しました。');
    }
}

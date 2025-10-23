<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * 🔐 管理者ログイン画面
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * 🔑 管理者ログイン処理
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ])->onlyInput('email');
    }

    /**
     * 🚪 管理者ログアウト
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * 🧩 一般ユーザー⇄管理者 権限切替（同一グループ内のみ）
     */
    public function toggleAdmin(User $user)
    {
        $this->authorizeAdminAccess();

        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')->with('info', '先にグループを選択してください。');
        }

        // ✅ 他グループのユーザーを操作できないように制限
        if ($user->group_id !== $groupId) {
            abort(403, 'このユーザーを操作する権限がありません。');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return redirect()->back()->with('success', 'ユーザー権限を更新しました。');
    }

    /**
     * ⚙️ 管理設定画面（グループ限定）
     */
    public function settings()
    {
        $this->authorizeAdminAccess();

        $groupId = session('selected_group_id');

        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        // ✅ group_id カラムが存在するか安全に確認
        if (\Schema::hasColumn('users', 'group_id')) {
            $users = User::where('group_id', $groupId)->orderBy('name')->get();
        } else {
            // ✅ 一時的な fallback（全ユーザー表示）
            $users = User::orderBy('name')->get();
        }

        return view('admin.dashboard', compact('users', 'groupId'));
    }

    /**
     * 🧑‍💼 自分自身の管理者権限を切り替える（一般ユーザー→管理者）
     */
    public function toggleSelf()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'ログインしてください。');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $msg = $user->is_admin
            ? '✅ 管理者権限を付与しました。'
            : '👤 一般ユーザーに戻しました。';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * 🛡️ 管理者権限を強制チェック
     */
    private function authorizeAdminAccess()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, '管理者権限が必要です。');
        }
    }
}

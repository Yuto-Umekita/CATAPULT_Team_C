<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * 🏠 管理者ダッシュボード（グループ内ユーザーのみ表示）
     */
    public function dashboard()
    {
        $currentUser = Auth::user();

        // ✅ 自分が所属しているグループIDを取得
        $groupId = $currentUser->group_id;

        // ✅ 同じグループのユーザーだけ取得（未所属は除外）
        $users = User::where('group_id', $groupId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('users'));
    }

    // 他のメソッド（settings, toggleAdmin, toggleSelf）はそのままでOK
}

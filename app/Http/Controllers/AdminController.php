<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\GroupMember;

class AdminController extends Controller
{
    public function dashboard()
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        // ✅ 現在のグループに所属するユーザーのみ取得
        $users = User::whereIn('id', function ($query) use ($groupId) {
            $query->select('user_id')
                ->from('group_members')
                ->where('group_id', $groupId);
        })->orderBy('id')->get();

        $currentUser = Auth::user();

        return view('admin.dashboard', compact('users', 'currentUser'));
    }

    /**
    * 🌀 管理者が自分の権限を切り替える（開発・デバッグ用）
    */
    public function toggleSelf(Request $request)
    {
        $user = \Auth::user();

        // 管理者権限をトグル（admin <-> 一般）
        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? '管理者' : '一般ユーザー';

        return back()->with('success', "あなたの権限を「{$status}」に切り替えました。");
    }

}


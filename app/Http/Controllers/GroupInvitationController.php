<?php

namespace App\Http\Controllers;

use App\Models\GroupInvitation;
use Illuminate\Support\Facades\Auth;

class GroupInvitationController extends Controller
{
    /**
     * 招待リンクを受け取ったときの処理
     */
    public function accept($token)
    {
        $invitation = GroupInvitation::where('token', $token)->firstOrFail();

        if ($invitation->accepted) {
            return redirect('/login')->with('info', 'この招待はすでに承認されています。');
        }

        // 未ログインならログイン・登録ページへ
        if (!Auth::check()) {
            session(['pending_invite_token' => $token]);
            return redirect()->route('register')->with('info', '登録完了後にグループへ参加できます。');
        }

        // ログイン済み → グループに追加
        $invitation->group->members()->attach(Auth::id(), ['role' => 'member']);
        $invitation->update(['accepted' => true]);

        return redirect()->route('groups.index')->with('success', 'グループに参加しました！');
    }
}

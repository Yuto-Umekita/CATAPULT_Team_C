<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\GroupInvitationMail;

class GroupMemberController extends Controller
{
    /**
     * メンバー一覧
     */
    public function index(Group $group)
    {
        $this->authorizeGroup($group);
        $members = $group->members()->get();

        return view('groups.members.index', compact('group', 'members'));
    }

    /**
     * メンバー追加フォーム表示
     */
    public function create(Group $group)
    {
        $this->authorizeGroup($group);
        return view('groups.members.create', compact('group'));
    }

    /**
     * メンバー追加処理
     * - 登録済みユーザー：即追加
     * - 未登録ユーザー：招待メール送信
     */
    public function store(Request $request, Group $group)
    {
        $this->authorizeGroup($group);

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $email = $validated['email'];
        $user = User::where('email', $email)->first();

        if ($user) {
            // ✅ 登録済ユーザー：すでに所属しているか確認
            if ($group->members()->where('user_id', $user->id)->exists()) {
                return back()->with('error', 'このユーザーはすでにグループに参加しています。');
            }

            // 即追加
            $group->members()->attach($user->id, ['role' => 'member']);
            return redirect()
                ->route('group.members.index', $group)
                ->with('success', "{$user->name} をグループに追加しました。");
        } else {
            // 🚀 未登録ユーザー：招待処理
            $existingInvitation = GroupInvitation::where('group_id', $group->id)
                ->where('email', $email)
                ->where('accepted', false)
                ->first();

            if ($existingInvitation) {
                return back()->with('info', 'このユーザーにはすでに招待メールを送信しています。');
            }

            $token = Str::uuid()->toString();

            $invitation = GroupInvitation::create([
                'group_id' => $group->id,
                'inviter_id' => Auth::id(),
                'email' => $email,
                'token' => $token,
            ]);

            try {
                Mail::to($email)->send(new GroupInvitationMail($invitation));
                return back()->with('success', '未登録ユーザーに招待メールを送信しました。');
            } catch (\Exception $e) {
                return back()->with('error', 'メール送信に失敗しました。管理者にお問い合わせください。');
            }
        }
    }

    /**
     * メンバー削除処理
     */
    public function destroy(Group $group, User $user)
    {
        $this->authorizeGroup($group);

        // 自分自身を削除する場合は警告
        if ($user->id === Auth::id()) {
            return back()->with('error', '自分自身は削除できません。');
        }

        $group->members()->detach($user->id);

        return back()->with('success', "{$user->name} をグループから削除しました。");
    }

    /**
     * 権限チェック（グループ作成者のみ許可）
     */
    private function authorizeGroup(Group $group)
    {
        if ($group->user_id !== Auth::id()) {
            abort(403, 'この操作を行う権限がありません。');
        }
    }
}

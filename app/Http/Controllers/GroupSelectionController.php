<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Group;


class GroupSelectionController extends Controller
{
    /**
     * 🌈 グループ選択画面を表示
     */
    public function select()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        $user = Auth::user();

        // ✅ 現在のモードを取得（セッション or プロフィール or household）
        $mode = Session::get('mode', optional($user->profile)->user_type ?? 'household');

        if (!$mode) {
            return redirect()->route('mode.select')->with('error', 'モードを選択してください。');
        }

        // ✅ 所属しているグループ（作成者 or メンバー）
        $groups = Group::with('members')
            ->where('mode', $mode)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereHas('members', function ($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })
            ->orderByDesc('created_at')
            ->get();

        // 🚨 グループが存在しない場合 → 作成画面へ
        if ($groups->isEmpty()) {
            $message = ($mode === 'household' ? '家庭用' : '企業用') . 'グループを作成してください。';
            return redirect()->route('groups.create')->with('info', $message);
        }

        return view('groups.select', compact('groups', 'mode'));
    }

    /**
     * 💾 選択されたグループをセッションに保存
     */
public function set(Request $request)
{
    $validated = $request->validate([
        'group_id' => 'required|exists:groups,id',
    ]);

    $group = Group::find($validated['group_id']);

    // ✅ 確実にセッションに保存
    $request->session()->put('selected_group_id', $group->id);


    // ✅ モードに応じてリダイレクト
    $redirectRoute = $group->mode === 'household'
        ? 'home.dashboard'
        : 'company.dashboard';

    return redirect()
        ->route($redirectRoute)
        ->with('success', "{$group->name}（" . ($group->mode === 'household' ? '家庭用' : '企業用') . "）を選択しました。");
}

}

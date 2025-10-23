<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Group;

class GroupController extends Controller
{
    /**
     * 🌈 グループ一覧を表示（現在のモードに応じてフィルタ）
     */
    public function index()
    {
        $userId = Auth::id();
        $currentMode = Session::get('mode', 'household');

        // 現在のモードに対応するグループのみ表示
        $groups = Group::where('user_id', $userId)
            ->where('mode', $currentMode)
            ->orderByDesc('created_at')
            ->get();

        // 現在選択中のグループ
        $selectedGroupId = Session::get('selected_group_id');
        $selectedGroup = $selectedGroupId ? Group::find($selectedGroupId) : null;

        return view('groups.index', compact('groups', 'currentMode', 'selectedGroup'));
    }

    /**
     * ➕ グループ作成フォームを表示
     */
    public function create()
    {
        $selectedMode = Session::get('mode');

        if (!$selectedMode) {
            return redirect()
                ->route('mode.select')
                ->with('error', 'モードを選択してください。');
        }

        return view('groups.create', compact('selectedMode'));
    }

    /**
     * 💾 グループを保存
     */
    public function store(Request $request)
    {
        $currentMode = Session::get('mode');

        if (!$currentMode) {
            return redirect()
                ->route('mode.select')
                ->with('error', 'モードを選択してください。');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // ✅ グループを作成
        $group = Group::create([
            'user_id' => Auth::id(),
            'name'    => $validated['name'],
            'mode'    => $currentMode,
        ]);

        // ✅ 自分自身を group_user に自動登録
        $group->members()->attach(Auth::id(), ['role' => 'admin']);

        // ✅ 作成したグループを自動選択状態にする
        Session::put('selected_group_id', $group->id);

        // Ajaxリクエスト時（モーダル対応）
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'group'   => $group,
                'message' => "グループ「{$group->name}」を作成しました。",
            ]);
        }

        // 通常リクエスト時：メニュー画面へ
        return redirect()
            ->route('menu.index')
            ->with('success', "グループ「{$group->name}」を作成し、選択しました。");
    }

    /**
     * ✏️ グループ編集フォームを表示
     */
    public function edit(Group $group)
    {
        $this->authorizeGroup($group);
        return view('groups.edit', compact('group'));
    }

    /**
     * 🔄 グループ更新処理
     */
    public function update(Request $request, Group $group)
    {
        $this->authorizeGroup($group);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update($validated);

        return redirect()
            ->route('groups.index')
            ->with('success', 'グループ名を更新しました。');
    }

    /**
     * 🗑️ グループ削除処理
     */
    public function destroy(Group $group)
    {
        $this->authorizeGroup($group);

        if (Session::get('selected_group_id') === $group->id) {
            Session::forget('selected_group_id');
        }

        $group->delete();

        return redirect()
            ->route('groups.index')
            ->with('success', 'グループを削除しました。');
    }

    /**
     * 🛡️ 権限確認
     */
    private function authorizeGroup(Group $group)
    {
        $currentMode = Session::get('mode');
        if ($group->user_id !== Auth::id() || $group->mode !== $currentMode) {
            abort(403, 'このグループを操作する権限がありません。');
        }
    }
}

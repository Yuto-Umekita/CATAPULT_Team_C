<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemoController extends Controller
{
    /**
     * ✍️ メモ作成フォーム表示
     */
    public function create(Item $item)
    {
        $this->authorizeGroupAccess($item);
        return view('items.memos.create', compact('item'));
    }

    /**
     * 💾 メモ登録処理
     */
    public function store(Request $request, Item $item)
    {
        $this->authorizeGroupAccess($item);

        $request->validate([
            'memo' => 'required|string|max:255',
        ]);

        $item->memos()->create([
            'memo' => $request->memo,
            'user_id' => Auth::id(),
            'group_id' => session('selected_group_id'),
        ]);

        return redirect()->route('items.show', $item)
            ->with('success', 'メモを追加しました。');
    }

    /**
     * 📄 メモ詳細
     */
    public function show(Item $item, Memo $memo)
    {
        $this->authorizeGroupAccess($item);

        // ✅ メモが同じアイテム・グループに属するか確認
        if ($memo->item_id !== $item->id || $memo->group_id !== $item->group_id) {
            abort(403, 'このメモを表示する権限がありません。');
        }

        return view('items.memos.show', compact('item', 'memo'));
    }

    /**
     * ✏️ メモ編集フォーム
     */
    public function edit(Item $item, Memo $memo)
    {
        $this->authorizeGroupAccess($item);

        if ($memo->item_id !== $item->id || $memo->group_id !== $item->group_id) {
            abort(403, 'このメモを編集する権限がありません。');
        }

        return view('items.memos.edit', compact('item', 'memo'));
    }

    /**
     * 🔄 メモ更新
     */
    public function update(Request $request, Item $item, Memo $memo)
    {
        $this->authorizeGroupAccess($item);

        if ($memo->item_id !== $item->id || $memo->group_id !== $item->group_id) {
            abort(403, 'このメモを更新する権限がありません。');
        }

        $request->validate([
            'memo' => 'required|string|max:255',
        ]);

        $memo->update(['memo' => $request->memo]);

        return redirect()->route('items.memos.show', [$item, $memo])
            ->with('success', 'メモを更新しました。');
    }

    /**
     * 🗑️ メモ削除
     */
    public function destroy(Item $item, Memo $memo)
    {
        $this->authorizeGroupAccess($item);

        if ($memo->item_id !== $item->id || $memo->group_id !== $item->group_id) {
            abort(403, 'このメモを削除する権限がありません。');
        }

        $memo->delete();

        return redirect()->route('items.show', $item)
            ->with('success', 'メモを削除しました。');
    }

    /**
     * 🛡️ グループ権限チェック（他グループデータ操作防止）
     */
    private function authorizeGroupAccess(Item $item)
    {
        $groupId = session('selected_group_id');

        if (!$groupId) {
            abort(403, 'グループが選択されていません。');
        }

        if ($item->group_id !== $groupId) {
            abort(403, 'このアイテムにアクセスする権限がありません。');
        }
    }
}

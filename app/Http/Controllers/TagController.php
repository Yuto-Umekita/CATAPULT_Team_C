<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * 📋 タグ一覧をJSONで返す（グループ単位）
     */
    public function index()
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return response()->json(['error' => 'グループが選択されていません。'], 400);
        }

        // 現在のグループのタグのみ取得
        $tags = Tag::where('group_id', $groupId)
            ->whereNull('item_id') // 全体タグのみ
            ->orderBy('id')
            ->get();

        return response()->json($tags);
    }

    /**
     * ➕ タグ作成（全体 or 商品別）
     */
    public function store(Request $request)
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return response()->json(['error' => 'グループが選択されていません。'], 400);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
        ]);

        // 商品に紐づくタグ登録
        if (!empty($validated['item_id'])) {
            $item = Item::where('id', $validated['item_id'])
                ->where('group_id', $groupId)
                ->firstOrFail();

            // 同じグループ内に同名タグがあれば再利用
            $tag = Tag::where('name', $validated['name'])
                ->where('group_id', $groupId)
                ->first();

            if (!$tag) {
                $tag = Tag::create([
                    'name' => $validated['name'],
                    'group_id' => $groupId,
                    'item_id' => null,
                ]);
            }

            $item->tags()->syncWithoutDetaching([$tag->id]);

            return response()->json(['success' => true, 'tag' => $tag]);
        }

        // 全体タグの場合
        $tag = Tag::firstOrCreate([
            'name' => $validated['name'],
            'group_id' => $groupId,
            'item_id' => null,
        ]);

        return response()->json(['success' => true, 'tag' => $tag]);
    }

    /**
     * ✏️ タグ名の更新（同一グループ限定）
     */
    public function update(Request $request, Tag $tag)
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return response()->json(['error' => 'グループが選択されていません。'], 400);
        }

        // 他グループのタグ操作禁止
        if ($tag->group_id !== $groupId) {
            return response()->json(['error' => 'このタグを編集する権限がありません。'], 403);
        }

        $validated = $request->validate(['name' => 'required|string|max:255']);
        $tag->update($validated);

        return response()->json(['success' => true, 'tag' => $tag]);
    }

    /**
     * 🗑️ タグ削除（同一グループ限定）
     */
    public function destroy(Tag $tag)
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return response()->json(['error' => 'グループが選択されていません。'], 400);
        }

        if ($tag->group_id !== $groupId) {
            return response()->json(['error' => 'このタグを削除する権限がありません。'], 403);
        }

        // 関連を解除して削除
        $tag->items()->detach();
        $tag->delete();

        return response()->json(['success' => true]);
    }
}

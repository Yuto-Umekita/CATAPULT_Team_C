<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\Request;

class ItemTagController extends Controller
{
    /**
     * 📋 指定アイテムのタグ一覧を取得（グループ限定）
     */
    public function index(Item $item)
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return response()->json(['error' => 'グループが選択されていません。'], 400);
        }

        // 🔒 他グループのアイテムは非表示
        if ($item->group_id !== $groupId) {
            return response()->json(['error' => 'このアイテムを操作する権限がありません。'], 403);
        }

        // ✅ 同一グループ内の全タグを取得
        $tags = Tag::where('group_id', $groupId)
            ->orderBy('name')
            ->get()
            ->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'checked' => $item->tags->contains($tag->id),
            ]);

        return response()->json($tags);
    }

    /**
     * 🔄 タグの付与・削除トグル（同一グループ限定）
     */
    public function toggle(Item $item, Request $request)
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return response()->json(['error' => 'グループが選択されていません。'], 400);
        }

        // グループ一致チェック
        if ($item->group_id !== $groupId) {
            return response()->json(['error' => 'このアイテムを操作する権限がありません。'], 403);
        }

        $data = $request->validate([
            'tag_id' => ['required', 'integer', 'exists:tags,id'],
            'checked' => ['required', 'boolean'],
        ]);

        // 対象タグをグループ制約付きで取得
        $tag = Tag::where('id', $data['tag_id'])
            ->where('group_id', $groupId)
            ->first();

        if (!$tag) {
            return response()->json(['error' => 'このタグは現在のグループに存在しません。'], 404);
        }

        // ✅ タグの付与 or 削除
        if ($data['checked']) {
            $item->tags()->syncWithoutDetaching([$tag->id]);
        } else {
            $item->tags()->detach($tag->id);
        }

        return response()->json(['ok' => true]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Item;

class TagController extends Controller
{
    // タグ一覧をJSONで返す
    public function index()
    {
        return response()->json(Tag::orderBy('id')->get());
    }

    // タグ作成（全体 or 商品別）
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
        ]);

        if (!empty($validated['item_id'])) {
            $item = Item::findOrFail($validated['item_id']);
            $tag = Tag::where('name', $validated['name'])->first();

            if (!$tag) {
                $tag = Tag::create([
                    'name' => $validated['name'],
                    'item_id' => null,
                ]);
            }

            $item->tags()->syncWithoutDetaching([$tag->id]);
            return response()->json(['success' => true, 'tag' => $tag]);
        }

        // 全体タグの場合
        $tag = Tag::firstOrCreate([
            'name' => $validated['name'],
            'item_id' => null,
        ]);

        return response()->json(['success' => true, 'tag' => $tag]);
    }

    // タグ更新
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $tag->update($validated);

        return response()->json(['success' => true, 'tag' => $tag]);
    }

    // タグ削除
    public function destroy(Tag $tag)
    {
        // アイテムとの関連を解除
        $tag->items()->detach();
        $tag->delete();

        return response()->json(['success' => true]);
    }
}

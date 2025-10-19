<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Item;

class TagController extends Controller
{
    /**
     * タグ一覧をJSONで返す
     * → 全体タグ + 商品タグの両方を返す（絞り込みに必要）
     */
    public function index()
    {
        return response()->json(Tag::orderBy('id')->get());
    }

    /**
     * タグを作成または商品に紐付け
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
        ]);

        // ✅ 商品に紐付けるタグの場合（item_idあり）
        if (!empty($validated['item_id'])) {
    $item = Item::findOrFail($validated['item_id']);

    // すでに同名タグが存在するか
    $tag = Tag::where('name', $validated['name'])->first();

    // なければ新規作成
    if (!$tag) {
        $tag = Tag::create([
            'name' => $validated['name'],
            'item_id' => null,
        ]);
    }

    // 紐付け
    if (method_exists($item, 'tags')) {
        $item->tags()->syncWithoutDetaching([$tag->id]);
    }

    return response()->json(['success' => true, 'tag' => $tag]);
    }   


    $tag = Tag::firstOrCreate([
        'name' => $validated['name'],
        'item_id' => null
    ]);

    return response()->json(['success' => true, 'tag' => $tag]);
    }

    /**
     * タグ名を更新（重複名も許可）
     */
public function update(Request $request, $id)
{
    try {
        // JSON対応
        $data = $request->json()->all();

        $validated = validator($data, [
            'name' => 'required|string|max:255',
        ])->validate();

        $tag = Tag::findOrFail($id);
        $tag->update(['name' => $validated['name']]);

        return response()->json(['success' => true, 'tag' => $tag]);
    } catch (\Throwable $e) {
        \Log::error('Tag update failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * タグ削除
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);

        if (method_exists($tag, 'items')) {
            $tag->items()->detach();
        }

        $tag->delete();
        return response()->json(['success' => true]);
    }
}



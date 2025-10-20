<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with([
            'user',
            'tags', // ✅ タグを常に読み込む
            'memos' => function ($q) {
                $q->latest()->with('user');
            },
        ])
        ->where('quantity', '>', 0); // ✅ 数量が1以上の在庫のみ表示

        // 🔍 商品名キーワード検索
        if ($request->filled('keyword')) {
            $query->where('item', 'like', '%' . $request->keyword . '%');
        }

        // 📦 在庫数範囲
        if ($request->filled('stock_min')) {
            $query->where('quantity', '>=', (int)$request->stock_min);
        }
        if ($request->filled('stock_max')) {
            $query->where('quantity', '<=', (int)$request->stock_max);
        }

        // 🗓️ 更新日範囲
        if ($request->filled('updated_from')) {
            $query->whereDate('updated_at', '>=', $request->updated_from);
        }
        if ($request->filled('updated_to')) {
            $query->whereDate('updated_at', '<=', $request->updated_to);
        }

        // ⏰ 賞味期限範囲
        if ($request->filled('expiration_from')) {
            $query->whereDate('expiration_date', '>=', $request->expiration_from);
        }
        if ($request->filled('expiration_to')) {
            $query->whereDate('expiration_date', '<=', $request->expiration_to);
        }

        $items = $query
            ->orderByDesc('pinned')  // ← ピン付き(true)を先に
            ->orderBy('updated_at', 'desc') // 更新日が新しい順
            ->get();


        // JSONリクエストならデータを返す
        if ($request->expectsJson()) {
            return response()->json($items);
        }

        // 通常リクエストならBladeを表示
        return view('items.index');
    }



    /**
     * 在庫登録フォーム表示
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * 在庫登録処理
     * - item_id を UUID で自動生成
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'expiration_year' => 'nullable|integer|min:2024|max:2100',
            'expiration_month' => 'nullable|integer|min:1|max:12',
            'expiration_day' => 'nullable|integer|min:1|max:31',
        ]);

        $item = new Item();
        $item->item_id = (string) Str::uuid(); // ✅ UUID 自動生成
        $item->item = $validated['item'];
        $item->quantity = $validated['quantity'];

        // ✅ 賞味期限を組み立て
        if ($request->filled(['expiration_year', 'expiration_month', 'expiration_day'])) {
            $item->expiration_date = sprintf(
                '%04d-%02d-%02d',
                $validated['expiration_year'],
                $validated['expiration_month'],
                $validated['expiration_day']
            );
        }

        $item->user_id = auth()->id();
        $item->save();

        return redirect()->route('items.index')
            ->with('success', '在庫を追加しました。');
    }

    /**
     * 詳細ページ
     */
    public function show($id)
    {
        $item = Item::with(['user', 'memos', 'tags'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    /**
     * 編集ページ
     */
    public function edit($id)
    {
        $item = Item::with(['tags'])->findOrFail($id);

        $expiration = ['year' => null, 'month' => null, 'day' => null];
        if ($item->expiration_date) {
            $expiration['year'] = $item->expiration_date->format('Y');
            $expiration['month'] = $item->expiration_date->format('m');
            $expiration['day'] = $item->expiration_date->format('d');
        }

        return view('items.edit', compact('item', 'expiration'));
    }

    /**
     * 在庫削除
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        return redirect()->route('items.index')->with('success', '在庫を削除しました。');
    }

    /**
     * ピン切り替え（Ajax）
     */
    public function togglePin(Item $item)
    {
        $item->pinned = !$item->pinned;
        $item->save();

        return response()->json(['pinned' => $item->pinned]);
    }
}

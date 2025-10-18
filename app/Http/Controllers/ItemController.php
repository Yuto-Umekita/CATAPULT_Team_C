<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    /**
     * 在庫一覧ページ
     * - JSONリクエスト時：Alpine.jsが使用（tags付きで返す）
     * - 通常リクエスト時：Bladeでページ表示
     */
    public function index(Request $request)
    {
        // ✅ 基本クエリ
        $query = Item::with([
            'user',
            'tags',
            'memos' => function ($q) {
                $q->latest()->with('user');
            }
        ]);

        // 🔍 商品名検索
        if ($request->filled('keyword')) {
            $query->where('item', 'like', '%' . $request->keyword . '%');
        }

        // 📦 在庫数フィルタ
        if ($request->filled('stock_min')) {
            $query->where('quantity', '>=', (int) $request->stock_min);
        }
        if ($request->filled('stock_max')) {
            $query->where('quantity', '<=', (int) $request->stock_max);
        }

        // 🗓 更新日フィルタ
        if ($request->filled('updated_from')) {
            $query->whereDate('updated_at', '>=', $request->updated_from);
        }
        if ($request->filled('updated_to')) {
            $query->whereDate('updated_at', '<=', $request->updated_to);
        }

        // ⏰ 賞味期限フィルタ
        if ($request->filled('expiration_from')) {
            $query->whereDate('expiration_date', '>=', $request->expiration_from);
        }
        if ($request->filled('expiration_to')) {
            $query->whereDate('expiration_date', '<=', $request->expiration_to);
        }

        // ✅ 並び順：
        // ① ピン付き優先（pinned=true が上）
        // ② 賞味期限が近い順（nullは一番下）
        // ③ 更新日が新しい順
        $query->orderByDesc('pinned')
              ->orderByRaw('CASE WHEN expiration_date IS NULL THEN 1 ELSE 0 END') // nullを後ろへ
              ->orderBy('expiration_date', 'asc')
              ->orderBy('updated_at', 'desc');

        // ✅ JSONレスポンス（Alpine.js用）
        if ($request->boolean('json')) {
            $items = $query->get()->map(function ($item) {
                $item->fade_key = uniqid('fade_'); // アニメーション用キー
                return $item;
            });
            return response()->json($items);
        }

        // ✅ 通常HTML表示（Blade用）
        $items = $query->paginate(12);
        $totalQuantity = $items->sum('quantity');

        return view('items.index', compact('items', 'totalQuantity'));
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

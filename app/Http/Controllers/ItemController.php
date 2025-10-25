<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * 📦 在庫一覧ページ
     */
    public function index(Request $request)
    {
        // ✅ グループ選択チェック
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')->with('info', '先にグループを選択してください。');
        }

        // ✅ 基本クエリ（選択グループに限定）
        $query = Item::with([
            'user',
            'tags',
            'memos' => function ($q) {
                $q->latest()->with('user');
            },
        ])
        ->where('group_id', $groupId)
        ->where('quantity', '>', 0);

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

        // ✅ 並び順：ピン付き → 更新日降順
        $items = $query
            ->orderByDesc('pinned')
            ->orderBy('updated_at', 'desc')
            ->get();

        // JSONレスポンス対応（Alpine.jsなど）
        if ($request->expectsJson()) {
            return response()->json($items);
        }

        // 通常リクエストならBladeを表示
        return view('items.index');
    }

    /**
     * ➕ 在庫登録フォーム
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * 💾 在庫登録処理
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

        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')->with('info', '先にグループを選択してください。');
        }

        $item = new Item();
        $item->item_id = (string) Str::uuid();
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

        $item->user_id = Auth::id();
        $item->group_id = $groupId;
        $item->save();

        return redirect()->route('items.index')
            ->with('success', '在庫を追加しました。');
    }

    /**
     * 🔍 詳細ページ
     */
    public function show($id)
    {
        $item = Item::with(['user', 'memos', 'tags'])->findOrFail($id);
        $this->authorizeGroupAccess($item);

        return view('items.show', compact('item'));
    }

    /**
     * ✏️ 編集ページ
     */
    public function edit($id)
    {
        $item = Item::with(['tags'])->findOrFail($id);
        $this->authorizeGroupAccess($item);

        $expiration = ['year' => null, 'month' => null, 'day' => null];
        if ($item->expiration_date) {
            $expiration['year'] = $item->expiration_date->format('Y');
            $expiration['month'] = $item->expiration_date->format('m');
            $expiration['day'] = $item->expiration_date->format('d');
        }

        return view('items.edit', compact('item', 'expiration'));
    }

    /**
    * 📝 アイテムを更新（グループ対応版）
    */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'expiration_year' => 'nullable|integer|min:2024|max:2100',
            'expiration_month' => 'nullable|integer|min:1|max:12',
            'expiration_day' => 'nullable|integer|min:1|max:31',
        ]);

        // ✅ グループ所属確認
        $currentGroupId = session('selected_group_id');
        if ($item->group_id !== $currentGroupId) {
            abort(403, 'このアイテムを編集する権限がありません。');
        }

        // ✅ フィールド更新
        $item->item = $validated['item'];
        $item->quantity = $validated['quantity'];

        // ✅ 賞味期限を組み立て（入力されている場合のみ）
        if ($request->filled(['expiration_year', 'expiration_month', 'expiration_day'])) {
            $item->expiration_date = sprintf(
                '%04d-%02d-%02d',
                $validated['expiration_year'],
                $validated['expiration_month'],
                $validated['expiration_day']
            );
        } else {
            $item->expiration_date = null;
        }

        $item->save();

        return redirect()
            ->route('items.index')
            ->with('success', '在庫情報を更新しました。');
    }




    /**
     * 🗑️ 在庫削除
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $this->authorizeGroupAccess($item);

        $item->delete();

        return redirect()->route('items.index')->with('success', '在庫を削除しました。');
    }

    /**
     * 📌 ピン切り替え（Ajax対応）
     */
    public function togglePin(Item $item)
    {
        $this->authorizeGroupAccess($item);

        $item->pinned = !$item->pinned;
        $item->save();

        return response()->json(['pinned' => $item->pinned]);
    }

    /**
     * 🛡️ グループ権限チェック
     */
    private function authorizeGroupAccess(Item $item)
    {
        $currentGroupId = session('selected_group_id');
        if ($item->group_id !== $currentGroupId) {
            abort(403, 'この在庫データを操作する権限がありません。');
        }
    }
}

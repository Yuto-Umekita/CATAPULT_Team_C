<?php

namespace App\Http\Controllers;

use App\Models\PurchaseList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseListController extends Controller
{
    /**
     * 🛒 購入予定品一覧を表示（現在のグループ限定）
     */
    public function index()
    {
        $groupId = session('selected_group_id');

        // ✅ グループ未選択時はリダイレクト
        if (!$groupId) {
            return redirect()->route('group.select')->with('info', '先にグループを選択してください。');
        }

        // ✅ 現在のグループのデータのみ取得
        $lists = PurchaseList::where('group_id', $groupId)
            ->orderByDesc('created_at')
            ->get();

        return view('purchase_lists.index', compact('lists'));
    }

    /**
     * 📝 新しい購入予定品を登録
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'purchase_date' => 'nullable|date',
        ]);

        $groupId = session('selected_group_id');

        if (!$groupId) {
            return redirect()->route('group.select')->with('info', '先にグループを選択してください。');
        }

        // ✅ ログインユーザーとグループを紐付けて登録
        PurchaseList::create([
            'item' => $validated['item'],
            'quantity' => $validated['quantity'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'user_id' => Auth::id(),
            'group_id' => $groupId,
        ]);

        return redirect()
            ->route('purchase_lists.index')
            ->with('success', '購入リストに商品を追加しました！');
    }

    /**
     * 🗑️ 購入予定品を削除（グループ権限チェック付き）
     */
    public function destroy(PurchaseList $purchaseList)
    {
        $currentGroupId = session('selected_group_id');

        if ($purchaseList->group_id !== $currentGroupId) {
            abort(403, 'この購入リストを削除する権限がありません。');
        }

        $purchaseList->delete();

        return redirect()
            ->route('purchase_lists.index')
            ->with('success', '削除しました。');
    }
}

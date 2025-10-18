<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;


class IngredientController extends Controller
{
    // 在庫カード一覧（ユーザー別ピンを上部セクションに固定表示）
    public function index(Request $request)
    {
        $user = $request->user();

        // ピン済み一覧（ユーザー別・順序付き）
        $pinned = Ingredient::query()
            ->join('user_ingredient_pins as uip', function ($join) use ($user) {
                $join->on('uip.ingredient_id', '=', 'ingredients.id')
                     ->where('uip.user_id', '=', $user->id);
            })
            ->select('ingredients.*', 'uip.pinned_order')
            ->orderBy('uip.pinned_order')
            ->get();

        $pinnedIds = $pinned->pluck('id')->all();

        // 非ピンのその他一覧（ページネーション）
        $others = Ingredient::query()
            ->when(!empty($pinnedIds), fn($q) => $q->whereNotIn('id', $pinnedIds))
            ->orderBy('name') // 好みで変更
            ->paginate(50)
            ->withQueryString();

        return view('ingredients.index', compact('pinned', 'others'));
    }

    // ピンのON/OFF切替（ユーザー別）
    public function togglePinUser(Request $request, Ingredient $ingredient)
    {
        $user = $request->user();

        $exists = DB::table('user_ingredient_pins')
            ->where('user_id', $user->id)
            ->where('ingredient_id', $ingredient->id)
            ->exists();

        if ($exists) {
            // ピン解除
            DB::table('user_ingredient_pins')
                ->where('user_id', $user->id)
                ->where('ingredient_id', $ingredient->id)
                ->delete();
        } else {
            // ピン追加（末尾に）
            $maxOrder = DB::table('user_ingredient_pins')
                ->where('user_id', $user->id)
                ->max('pinned_order');

            DB::table('user_ingredient_pins')->insert([
                'user_id'       => $user->id,
                'ingredient_id' => $ingredient->id,
                'pinned_order'  => is_null($maxOrder) ? 1 : ($maxOrder + 1),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // Ajax前提なら JSON を返す。通常遷移ならリダイレクトでもOK
        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back();
    }

    // ピンの並び替え（ドラッグ＆ドロップ）
    public function reorderPinsUser(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'ordered_ids'   => 'required|array',
            'ordered_ids.*' => 'integer|exists:ingredients,id',
        ]);

        $orderedIds = $validated['ordered_ids'];

        DB::transaction(function () use ($user, $orderedIds) {
            foreach ($orderedIds as $i => $ingredientId) {
                DB::table('user_ingredient_pins')->updateOrInsert(
                    ['user_id' => $user->id, 'ingredient_id' => $ingredientId],
                    ['pinned_order' => $i + 1, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        });

        return response()->json(['ok' => true]);
    }
}
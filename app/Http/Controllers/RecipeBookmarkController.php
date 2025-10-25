<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecipeBookmark;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class RecipeBookmarkController extends Controller
{
    /**
     * 📚 ブックマーク一覧ページを表示（グループ単位）
     */
    public function index()
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        $bookmarks = RecipeBookmark::where('group_id', $groupId)
            ->latest()
            ->get();

        return view('recipes.bookmarks', compact('bookmarks'));
    }

    /**
     * ➕ ブックマーク登録（DeepL翻訳付き）
     */
    public function store(Request $request)
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return back()->with('error', '先にグループを選択してください。');
        }

        $validated = $request->validate([
            'recipe_id' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'image_url' => 'nullable|string|max:255',
        ]);

        // 🇯🇵 DeepL翻訳（英語タイトル → 日本語）
        $translatedTitle = $validated['title'];
        try {
            $response = Http::asForm()->post('https://api-free.deepl.com/v2/translate', [
                'auth_key' => env('DEEPL_API_KEY'),
                'text' => $validated['title'],
                'target_lang' => 'JA',
            ]);
            $data = $response->json();
            if (isset($data['translations'][0]['text'])) {
                $translatedTitle = $data['translations'][0]['text'];
            }
        } catch (\Throwable $e) {
            logger('DeepL翻訳失敗: ' . $e->getMessage());
        }

        // ✅ ユーザー＋グループ単位で一意に保存
        RecipeBookmark::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'group_id' => $groupId,
                'recipe_id' => $validated['recipe_id'],
            ],
            [
                'title' => $validated['title'],
                'translated_title' => $translatedTitle,
                'image_url' => $validated['image_url'] ?? null,
            ]
        );

        return back()->with('message', 'ブックマークに追加しました！');
    }

    /**
     * 🗑️ ブックマーク削除（グループ単位）
     */
    public function destroy(RecipeBookmark $bookmark)
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return back()->with('error', '先にグループを選択してください。');
        }

        // ✅ ログインユーザー＋グループ一致確認（安全）
        if ($bookmark->group_id !== $groupId || $bookmark->user_id !== Auth::id()) {
            return back()->with('error', 'このブックマークを削除する権限がありません。');
        }

        $bookmark->delete();

        return back()->with('message', 'ブックマークを削除しました。');
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Item;
use App\Models\RecipeBookmark;

class RecipeController extends Controller
{
    /**
     * 在庫から作れるレシピ一覧を表示
     */
    public function index()
    {
        // -------------------------------------
        // 🧩 1. 在庫アイテムを取得
        // -------------------------------------
        $items = Item::pluck('item')->filter()->toArray();

        if (empty($items)) {
            return view('recipes.index', [
                'recipes' => [],
                'bookmarkedRecipeIds' => [],
                'message' => '在庫が登録されていません。',
            ]);
        }

        // DeepL エンドポイント
        $deeplUrl = env('DEEPL_API_URL', 'https://api-free.deepl.com/v2/translate');
        $deeplKey = env('DEEPL_API_KEY');

        // -------------------------------------
        // 🌐 2. 在庫名を英語に翻訳（DeepL + キャッシュ）
        // -------------------------------------
        $translatedIngredients = [];
        foreach ($items as $ingredient) {
            $cacheKey = 'deepl_en_' . md5($ingredient);
            $translatedIngredients[] = Cache::remember($cacheKey, 86400, function () use ($ingredient, $deeplUrl, $deeplKey) {
                try {
                    $res = Http::asForm()->post($deeplUrl, [
                        'auth_key'    => $deeplKey,
                        'text'        => $ingredient,
                        'target_lang' => 'EN',
                    ]);
                    $data = $res->json();
                    return $data['translations'][0]['text'] ?? $ingredient;
                } catch (\Throwable $e) {
                    logger('DeepL翻訳エラー（在庫）', ['msg' => $e->getMessage()]);
                    return $ingredient;
                }
            });
        }

        // -------------------------------------
        // 🍳 3. Spoonacular APIでレシピ取得
        // -------------------------------------
        $recipes = [];
        $query   = implode(',', $translatedIngredients);

        try {
            $response = Http::get('https://api.spoonacular.com/recipes/findByIngredients', [
                'apiKey'     => env('SPOONACULAR_API_KEY'),
                'ingredients'=> $query,
                'number'     => 20,
                'ranking'    => 1,
            ]);

            if ($response->successful()) {
                $recipes = $response->json();
            } else {
                logger('Spoonacular API エラー', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Throwable $e) {
            logger('Spoonacular通信例外', ['msg' => $e->getMessage()]);
        }

        // -------------------------------------
        // 🇯🇵 4. レシピ名と食材名を日本語に翻訳
        // -------------------------------------
        foreach ($recipes as &$recipe) {
            // 🟩 タイトル翻訳
            if (isset($recipe['title'])) {
                $recipe['translated_title'] = $this->translateToJapanese($recipe['title'], $deeplUrl, $deeplKey);
            }

            // 🟦 使用食材翻訳
            if (!empty($recipe['usedIngredients'])) {
                foreach ($recipe['usedIngredients'] as &$ing) {
                    if (isset($ing['name'])) {
                        $ing['name'] = $this->translateToJapanese($ing['name'], $deeplUrl, $deeplKey);
                    }
                }
                unset($ing);
            }

            // 🟥 足りない食材翻訳
            if (!empty($recipe['missedIngredients'])) {
                foreach ($recipe['missedIngredients'] as &$ing) {
                    if (isset($ing['name'])) {
                        $ing['name'] = $this->translateToJapanese($ing['name'], $deeplUrl, $deeplKey);
                    }
                }
                unset($ing);
            }
        }
        unset($recipe);

        // -------------------------------------
        // ⭐ 5. ブックマーク済みのレシピID
        // -------------------------------------
        $bookmarkedRecipeIds = Auth::check()
            ? RecipeBookmark::where('user_id', Auth::id())->pluck('recipe_id')->toArray()
            : [];

        // -------------------------------------
        // 🖥️ 6. ビューへ
        // -------------------------------------
        return view('recipes.index', [
            'recipes' => $recipes,
            'bookmarkedRecipeIds' => $bookmarkedRecipeIds,
            'message' => count($recipes) ? null : '該当するレシピが見つかりませんでした。',
        ]);
    }

    /**
     * DeepLで日本語に翻訳（キャッシュ付き）
     */
    private function translateToJapanese(string $text, string $url, string $key): string
    {
        $cacheKey = 'deepl_ja_' . md5($text);
        return Cache::remember($cacheKey, 86400, function () use ($text, $url, $key) {
            try {
                $res = Http::asForm()->post($url, [
                    'auth_key'    => $key,
                    'text'        => $text,
                    'target_lang' => 'JA',
                ]);
                $data = $res->json();
                return $data['translations'][0]['text'] ?? $text;
            } catch (\Throwable $e) {
                logger('DeepL翻訳エラー', ['msg' => $e->getMessage(), 'text' => $text]);
                return $text;
            }
        });
    }
}

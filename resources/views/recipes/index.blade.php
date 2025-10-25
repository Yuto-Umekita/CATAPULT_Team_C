<x-app-layout>
    {{-- Font Awesome 読み込み（★アイコン用） --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <x-slot name="header">
        {{-- ✅ タイトルを中央、ボタンを右端に配置 --}}
        <div class="relative max-w-6xl mx-auto px-4">
            {{-- 🌙 タイトル（中央寄せ） --}}
            <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight text-center">
                {{ __('レシピ一覧') }}
            </h2>

            {{-- 🔖 ブックマーク一覧ページへのボタン（右上配置） --}}
            <a href="{{ route('bookmarks.index') }}"
               class="absolute right-0 top-1/2 -translate-y-1/2
                      inline-flex items-center justify-center
                      px-4 py-2 sm:px-5 bg-blue-500 text-white text-sm font-semibold rounded-lg shadow 
                      hover:bg-blue-600 transition
                      sm:w-auto w-40 text-center">
                🔖 ブックマーク一覧を見る
            </a>
        </div>
    </x-slot>



    <div class="py-8 max-w-6xl mx-auto px-4">
        {{-- メッセージがあれば表示 --}}
        @isset($message)
            <p class="text-center text-gray-600 dark:text-gray-300 mb-4">{{ $message }}</p>
        @endisset

        {{-- レシピが存在するか --}}
        @if(!empty($recipes) && is_iterable($recipes))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($recipes as $recipe)
                    @if(is_array($recipe) && isset($recipe['title']))
                        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col justify-between hover:shadow-lg transition">

                            {{-- ★ ブックマークボタン（右下固定） --}}
                            <div class="absolute bottom-3 right-3">
                                @php
                                    $isBookmarked = in_array($recipe['id'], $bookmarkedRecipeIds ?? []);
                                @endphp

                                @if($isBookmarked)
                                    <form method="POST" action="{{ route('bookmarks.destroy', ['bookmark' => $bookmark->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                            <button type="submit"
                                                class="text-yellow-400 hover:text-red-500 text-2xl transition"
                                                title="ブックマーク解除">
                                                <i class="fas fa-bookmark"></i>
                                            </button>
                                    </form>


                                @else
                                    <form method="POST" action="{{ route('bookmarks.store') }}">
                                        @csrf
                                        <input type="hidden" name="recipe_id" value="{{ $recipe['id'] }}">
                                        <input type="hidden" name="title" value="{{ $recipe['title'] }}">
                                        <input type="hidden" name="image_url" value="{{ $recipe['image'] ?? '' }}">
                                        <button type="submit"
                                            class="text-gray-400 hover:text-yellow-400 text-2xl transition transform hover:scale-125"
                                            title="ブックマーク">
                                            <i class="far fa-bookmark"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- タイトル --}}
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2 text-center">
                                {{ $recipe['translated_title'] ?? $recipe['title'] }}
                            </h3>

                            {{-- 画像（無い場合はダミー） --}}
                            <img src="{{ $recipe['image'] ?? 'https://via.placeholder.com/400x300?text=No+Image' }}"
                                 alt="{{ $recipe['title'] }}"
                                 class="w-full h-52 object-cover rounded mb-3">

                            {{-- 使用食材 --}}
                            @if(!empty($recipe['usedIngredients']) && is_array($recipe['usedIngredients']))
                                <p class="text-gray-700 dark:text-gray-300 text-sm">
                                    使用食材:
                                    {{ collect($recipe['usedIngredients'])->pluck('name')->implode(', ') }}
                                </p>
                            @endif

                            {{-- 足りない食材 --}}
                            @if(!empty($recipe['missedIngredients']) && is_array($recipe['missedIngredients']))
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                                    足りない食材:
                                    {{ collect($recipe['missedIngredients'])->pluck('name')->implode(', ') }}
                                </p>
                            @endif

                            {{-- 詳細ボタン --}}
                            <div class="text-center mt-4">
                                <a href="https://spoonacular.com/recipes/{{ Str::slug($recipe['title']) }}-{{ $recipe['id'] ?? '' }}"
                                   target="_blank"
                                   class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                   詳細を見る
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 dark:text-gray-400 mt-8">
                作れる料理は見つかりませんでした。
            </p>
        @endif
    </div>
</x-app-layout>

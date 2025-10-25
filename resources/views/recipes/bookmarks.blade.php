<x-app-layout>
    {{-- Font Awesome（★アイコン用） --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <x-slot name="header">
            <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight text-center">
                {{ __('ブックマークしたレシピ一覧') }}
            </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4">
        {{-- メッセージ --}}
        @if (session('message'))
            <p class="text-center text-green-600 mb-4">{{ session('message') }}</p>
        @endif

        @if (session('error'))
            <p class="text-center text-red-600 mb-4">{{ session('error') }}</p>
        @endif

        {{-- ブックマークがない場合 --}}
        @if ($bookmarks->isEmpty())
            <p class="text-center text-gray-500 mt-8">ブックマークしたレシピはありません。</p>
        @else
            {{-- レシピカード一覧 --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($bookmarks as $bookmark)
                    <div class="relative bg-white rounded-lg shadow p-4 flex flex-col justify-between hover:shadow-lg transition">

                        {{-- ★ ブックマーク解除ボタン（右下） --}}
                        <div class="absolute bottom-3 right-3">
                            <form method="POST" action="{{ route('bookmarks.destroy', ['bookmark' => $bookmark->id]) }}">
                                @csrf
                                @method('DELETE')
                                    <button type="submit"
                                        class="text-yellow-400 hover:text-red-500 text-2xl transition"
                                        title="ブックマーク解除">
                                        <i class="fas fa-bookmark"></i>
                                    </button>
                            </form>


                        </div>

                        {{-- 🇯🇵 タイトル（翻訳済み優先） --}}
                        <h3 class="text-lg font-bold text-gray-800 mb-2 text-center">
                            {{ $bookmark->translated_title ?? $bookmark->title }}
                        </h3>

                        {{-- 画像 --}}
                        <img src="{{ $bookmark->image_url ?? 'https://via.placeholder.com/400x300?text=No+Image' }}"
                             alt="{{ $bookmark->title }}"
                             class="w-full h-52 object-cover rounded mb-3">

                        {{-- 詳細ボタン --}}
                        <div class="text-center mt-4">
                            <a href="https://spoonacular.com/recipes/{{ Str::slug($bookmark->title) }}-{{ $bookmark->recipe_id }}"
                               target="_blank"
                               class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                詳細を見る
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

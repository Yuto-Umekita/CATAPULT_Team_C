<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            📊 {{ __('在庫CSVインポート・エクスポート') }}
        </h2>
    </x-slot>

    {{-- 🌟 メインコンテナ --}}
    <div class="max-w-3xl mx-auto mt-10 p-6 bg-white dark:bg-gray-800 shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">

        {{-- ✅ ステータスメッセージ --}}
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg text-center font-medium">
                {{ session('status') }}
            </div>
        @endif

        {{-- ✅ エラーメッセージ --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 📤 CSVエクスポート --}}
        <section class="mb-10">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center gap-2">
                📤 在庫CSVをエクスポート
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                現在登録されている在庫データをCSV形式でダウンロードできます。<br>
                Excelなどで開ける形式で出力されます。
            </p>

            {{-- ⚠️ GETでアクセスすると404なので必ずPOSTフォーム --}}
            <form action="{{ route('items.csv.export') }}" method="POST">
                @csrf
                <x-primary-button class="px-5 py-2">
                    💾 CSVをダウンロード
                </x-primary-button>
            </form>
        </section>

        <hr class="my-8 border-gray-300 dark:border-gray-600">

        {{-- 📥 CSVインポート --}}
        <section>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2 flex items-center gap-2">
                📥 在庫CSVをインポート
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                テンプレートを使用して編集したCSVをアップロードすると、在庫データが一括で登録・更新されます。<br>
                ファイル形式は <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">.csv</code> にしてください。
            </p>

            <form action="{{ route('items.csv.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <input 
                        type="file" 
                        name="csv_file" 
                        accept=".csv"
                        required
                        class="block w-full text-sm text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-400 p-2 bg-white dark:bg-gray-700 transition" />
                    <x-primary-button class="px-5 py-2">
                        ⬆️ アップロード
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6 text-sm">
                <a href="{{ route('items.csv.template') }}"
                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline">
                    🧾 テンプレートをダウンロード
                </a>
            </div>
        </section>
    </div>

    {{-- 🧭 戻るボタン --}}
    <div class="max-w-3xl mx-auto mt-10 mb-8 text-center">
        <a href="{{ route('menu.index') }}"
           class="inline-block text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white text-sm underline transition">
            ← メニューに戻る
        </a>
    </div>
</x-app-layout>

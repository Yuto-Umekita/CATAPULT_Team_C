<!DOCTYPE html>
<html 
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  x-data="{
      darkMode: localStorage.getItem('theme') === 'dark',
      toggleTheme() {
          this.darkMode = !this.darkMode;
          localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
          document.documentElement.classList.toggle('dark', this.darkMode);
      }
  }"
  x-init="document.documentElement.classList.toggle('dark', darkMode)"
  x-bind:class="{ 'dark': darkMode }"
>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('images/ielog-icon.svg') }}" type="image/svg+xml">
    <title>{{ config('app.name', 'IeLog') }}</title>

    <!-- 🖋 フォント -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- 🌀 Tailwind + Alpine + Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>

    <!-- ✨ Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => lucide.createIcons());
    </script>

    <!-- 💡 ダークモード保持 -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                toggle() {
                    this.dark = !this.dark;
                    localStorage.setItem('darkMode', this.dark);
                },
                dark: localStorage.getItem('darkMode') === 'true'
            });
        });
    </script>
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-screen flex flex-col">
        
        {{-- 🌐 ナビゲーションバー --}}
        <nav class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
                
                <!-- 左側：ロゴ -->
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/ielog-icon.svg') }}" alt="IeLog Icon" class="w-8 h-8">
                    <span class="text-xl font-bold text-brand-primary">IeLog</span>
                </div>

                <!-- 右側：操作ボタン群 -->
                <div class="flex items-center space-x-4">
                    
                <!-- 🌙 ダークモード切替 -->
                <button 
                    @click="toggleTheme()" 
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                    title="テーマ切り替え"
                >
                    <!-- 🌙 ライトモード時 -->
                    <template x-if="!darkMode">
                        <i data-lucide="moon" class="w-5 h-5 text-gray-600 dark:text-yellow-300"></i>
                    </template>

                    <!-- ☀️ ダークモード時 -->
                    <template x-if="darkMode">
                        <i data-lucide="sun" class="w-5 h-5 text-yellow-400 dark:text-yellow-200"></i>
                    </template>
                </button>


                    <!-- 🏠 メニュー -->
                    <a href="{{ route('menu.index') }}" 
                       class="flex items-center gap-1 px-3 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition">
                        <i data-lucide="grid" class="w-5 h-5"></i>
                        <span class="hidden sm:inline">メニュー</span>
                    </a>

                    <!-- ⚙️ 設定（メニューの右） -->
                    <a href="{{ route('settings.index') }}" 
                       class="flex items-center gap-1 px-3 py-2 rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">
                        <i data-lucide="settings" class="w-5 h-5"></i>
                        <span class="hidden sm:inline">設定</span>
                    </a>

                    <!-- 👤 プロフィール -->
                    <a href="{{ route('profile.view') }}" class="flex items-center gap-2 hover:opacity-80">
                        <i data-lucide="user-circle" class="w-6 h-6 text-gray-700 dark:text-gray-200"></i>
                        <span class="hidden sm:inline">プロフィール</span>
                    </a>
                </div>
            </div>
        </nav>

        {{-- 🧭 ページヘッダー --}}
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- 📄 メインコンテンツ --}}
        <main class="flex-1 bg-[#fdf4f4ff] dark:bg-gray-900 transition-colors duration-300">
            {{ $slot }}
        </main>

                {{-- 📌 フッター --}}
        <footer class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-t border-gray-200 dark:border-gray-700 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
            © {{ date('Y') }} IeLog.
        </footer>
    </div>

    {{-- ✅ 各ページ固有スクリプト（Alpine.jsなどを@push('scripts')で追加した場合） --}}
    @stack('scripts')

</body>
</html>


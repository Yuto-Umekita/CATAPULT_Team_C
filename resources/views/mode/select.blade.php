<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight text-center">
            {{ __('ログインモードの選択') }}
        </h2>
    </x-slot>

    <div class="py-16 flex flex-col items-center justify-center text-center px-4">
        {{-- 💬 説明文 --}}
        <p class="text-gray-700 dark:text-gray-300 text-lg mb-8">
            ご利用モードを選択してください。<br>
            選択後、対応するグループを選択または作成できます。
        </p>

        {{-- ✅ モード選択フォーム --}}
        <form method="POST" action="{{ route('mode.store') }}">
            @csrf

            <div class="flex flex-col sm:flex-row gap-6">
                {{-- 🏠 家庭用 --}}
                <button
                    type="submit"
                    name="user_type"
                    value="home"
                    class="flex flex-col items-center justify-center bg-pink-500 hover:bg-pink-600 text-white font-semibold py-6 px-12 rounded-2xl shadow-lg transition transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-pink-300 dark:focus:ring-pink-800">
                    <i data-lucide="home" class="w-8 h-8 mb-2"></i>
                    家庭用モード
                </button>

                {{-- 🏢 企業用 --}}
                <button
                    type="submit"
                    name="user_type"
                    value="company"
                    class="flex flex-col items-center justify-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-6 px-12 rounded-2xl shadow-lg transition transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                    <i data-lucide="building-2" class="w-8 h-8 mb-2"></i>
                    企業用モード
                </button>
            </div>
        </form>

        {{-- ℹ️ エラーメッセージ表示 --}}
        @if (session('error'))
            <div class="mt-8 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg w-full max-w-md">
                {{ session('error') }}
            </div>
        @endif

        {{-- 🌟 情報メッセージ --}}
        @if (session('info'))
            <div class="mt-8 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg w-full max-w-md">
                {{ session('info') }}
            </div>
        @endif

        {{-- 🎉 成功メッセージ --}}
        @if (session('success'))
            <div class="mt-8 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg w-full max-w-md">
                {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- Lucideアイコン初期化 --}}
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                if (window.lucide) lucide.createIcons();
            });
        </script>
    @endpush
</x-app-layout>

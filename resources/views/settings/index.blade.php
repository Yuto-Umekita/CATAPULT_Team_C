<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-gray-100 leading-tight">
      ⚙️ {{ __('設定') }}
    </h2>
  </x-slot>

  {{-- 🌙 Alpine.js & Lucide --}}
  <script src="https://unpkg.com/alpinejs" defer></script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <div class="max-w-5xl mx-auto py-10 px-6 space-y-10">

    {{-- ✅ 成功メッセージ --}}
    @if (session('success'))
      <div class="bg-green-100 text-green-800 px-4 py-3 rounded-xl shadow text-center">
        {{ session('success') }}
      </div>
    @endif

    {{-- 🔔 通知設定 --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 sm:p-8 
        scale-[0.95] sm:scale-100 transition-all duration-300">
      <h3 class="text-xl sm:text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <i data-lucide="bell" class="w-6 h-6 text-indigo-500"></i>
        通知設定
      </h3>

      <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
        @csrf

        {{-- 🧮 在庫閾値入力欄 --}}
        <div class="flex flex-col gap-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <label class="text-gray-700 dark:text-gray-200 font-semibold">
            「在庫が少ない」とみなす数量
          </label>
          <input type="number" name="low_stock_threshold" min="1" max="50"
            value="{{ old('low_stock_threshold', $user->low_stock_threshold) }}"
            class="w-24 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-2">
          <p class="text-sm text-gray-500 dark:text-gray-400">
            ※ ここで指定した個数未満を「在庫が少ない」として通知します。
          </p>
        </div>

        <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer">
          <span class="text-gray-700 dark:text-gray-200">在庫が少なくなったら通知</span>
          <input type="checkbox" name="notify_low_stock" class="w-5 h-5 accent-indigo-500"
            {{ $user->notify_low_stock ? 'checked' : '' }}>
        </label>

        <label class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer">
          <span class="text-gray-700 dark:text-gray-200">システムメンテナンス情報</span>
          <input type="checkbox" name="notify_system" class="w-5 h-5 accent-indigo-500"
            {{ $user->notify_system ? 'checked' : '' }}>
        </label>

        <div class="text-right pt-4">
          <button type="submit" class="px-5 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition">
            保存
          </button>
        </div>
      </form>
    </div>

    {{-- 🧾 アカウント情報 --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 sm:p-8 
        scale-[0.95] sm:scale-100 transition-all duration-300">
      <h3 class="text-xl sm:text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <i data-lucide="user" class="w-6 h-6 text-indigo-500"></i>
        アカウント情報
      </h3>

      <div class="flex flex-col sm:flex-row justify-between sm:items-center">
        <div>
          <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold">{{ $user->name }}</p>
          <p class="text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex justify-end sm:justify-start">
          <a href="{{ route('profile.edit') }}" 
            class="inline-flex items-center gap-2 bg-gray-700 dark:bg-indigo-600 text-white 
                  px-3 sm:px-5 py-2 text-sm sm:text-base rounded-lg
                  hover:bg-gray-800 dark:hover:bg-indigo-700 transition">
            <i data-lucide="edit-3" class="w-5 h-5"></i>
            プロフィールを編集
          </a>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      if (window.lucide) lucide.createIcons();
    });
  </script>
</x-app-layout>

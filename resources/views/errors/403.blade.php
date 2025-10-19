<x-app-layout>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- 🚫 エラー番号 -->
    <h1 class="text-7xl font-extrabold text-red-600 mb-4">403</h1>

    <!-- 📝 エラーメッセージ -->
    <p class="text-xl text-gray-800 dark:text-gray-200 mb-8">
      アクセス権限がありません。
    </p>

    <!-- 🏠 メニューに戻るボタン -->
    <a href="{{ route('menu.index') }}" 
       class="px-6 py-3 bg-indigo-600 text-white text-lg font-semibold rounded-xl hover:bg-indigo-700 transition-all duration-300 shadow-lg">
       メニューに戻る
    </a>
  </div>
</x-app-layout>

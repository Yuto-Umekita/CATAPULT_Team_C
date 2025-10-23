<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 text-center">
      新しいグループを作成
    </h2>
  </x-slot>

  <div class="max-w-md mx-auto mt-10 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
    <form method="POST" action="{{ route('groups.store') }}">
      @csrf

      <div class="mb-6">
        <label class="block text-gray-700 dark:text-gray-300 mb-2">グループ名</label>
        <input type="text" name="name"
               class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
               placeholder="例：家族のキッチン在庫" required>
      </div>

      <div class="mb-6">
        <label class="block text-gray-700 dark:text-gray-300 mb-2">モード</label>
        <input type="text" value="{{ $selectedMode === 'household' ? '家庭用' : '企業用' }}"
               disabled
               class="w-full border rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
      </div>

      <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
        グループを作成
      </button>
    </form>
  </div>
</x-app-layout>

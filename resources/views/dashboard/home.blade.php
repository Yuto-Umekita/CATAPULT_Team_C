{{-- resources/views/dashboard/home.blade.php --}}
<x-app-layout>
  {{-- 🌟 ヘッダー --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl sm:text-2xl text-gray-800 dark:text-gray-100 leading-tight text-center">
      🏠 家庭用通知
    </h2>
  </x-slot>

  {{-- 🔔 通知エリア --}}
  <div class="px-4 sm:px-6 mt-6 sm:mt-10">
    @if (!empty($notifications) && count($notifications) > 0)
      <div class="bg-indigo-50 dark:bg-indigo-900/40 border border-indigo-300 dark:border-indigo-700 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm">
        <h3 class="text-lg sm:text-xl font-semibold text-indigo-700 dark:text-indigo-300 mb-3 flex items-center gap-2">
          <i data-lucide="bell-ring" class="w-5 h-5 sm:w-6 sm:h-6"></i>
          通知センター
        </h3>

        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
          あなたの在庫閾値設定：
          <span class="font-semibold text-indigo-700 dark:text-indigo-200">{{ $threshold }}</span> 個未満
        </p>

        <ul class="list-disc list-inside text-gray-800 dark:text-gray-200 text-sm sm:text-base space-y-1">
          @foreach ($notifications as $note)
            <li>{{ $note }}</li>
          @endforeach
        </ul>
      </div>
    @else
      <div class="bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-center text-gray-600 dark:text-gray-300">
        現在通知はありません。
        <p class="mt-2 text-xs sm:text-sm">
          あなたの在庫閾値設定：
          <span class="font-semibold text-indigo-700 dark:text-indigo-200">{{ $threshold }}</span> 個未満
        </p>
      </div>
    @endif
  </div>

  {{-- 💡 メインコンテンツ --}}
  <div class="py-8 sm:py-12 bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-8 sm:space-y-12">

      {{-- 📦 賞味期限切れ --}}
      <section class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-lg border-l-4 sm:border-l-8 border-red-500 p-4 sm:p-6">
        <h3 class="text-lg sm:text-2xl font-bold text-red-600 dark:text-red-400 flex items-center gap-2 mb-3 sm:mb-4">
          🚨 賞味期限切れの商品
        </h3>

        @if($expiredItems->isEmpty())
          <p class="text-gray-500 dark:text-gray-300 text-center py-4 text-sm sm:text-lg">
            ✨ 現在、期限切れの商品はありません。
          </p>
        @else
          <div class="overflow-x-auto rounded-lg border border-red-100 dark:border-red-700">
            <table class="w-full border-collapse text-xs sm:text-base">
              <thead class="bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-200">
                <tr>
                  <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">商品名</th>
                  <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">賞味期限</th>
                  <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">個数</th>
                </tr>
              </thead>
              <tbody>
                @foreach($expiredItems as $item)
                  <tr class="border-b hover:bg-red-50 dark:hover:bg-red-800/40 transition">
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center font-medium text-red-700 dark:text-red-300">
                      {{ $item->item }}
                    </td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-700 dark:text-gray-200">
                      {{ $item->expiration_date->format('Y/m/d') }}
                    </td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-800 dark:text-gray-100">
                      {{ $item->quantity }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>

      {{-- ⏰ 期限間近 --}}
      <section class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-lg border-l-4 sm:border-l-8 border-yellow-500 p-4 sm:p-6">
        <h3 class="text-lg sm:text-2xl font-bold text-yellow-600 dark:text-yellow-300 flex items-center gap-2 mb-3 sm:mb-4">
          ⏳ 賞味期限が近い商品（7日以内）
        </h3>

        @if($nearExpiredItems->isEmpty())
          <p class="text-gray-500 dark:text-gray-300 text-center py-4 text-sm sm:text-lg">
            ✅ 期限間近の商品はありません。
          </p>
        @else
          <div class="overflow-x-auto rounded-lg border border-yellow-100 dark:border-yellow-700">
            <table class="w-full border-collapse text-xs sm:text-base">
              <thead class="bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200">
                <tr>
                  <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">商品名</th>
                  <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">賞味期限</th>
                  <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">個数</th>
                </tr>
              </thead>
              <tbody>
                @foreach($nearExpiredItems as $item)
                  <tr class="border-b hover:bg-yellow-50 dark:hover:bg-yellow-800/40 transition">
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-800 dark:text-gray-200">
                      {{ $item->item }}
                    </td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-700 dark:text-gray-300">
                      {{ $item->expiration_date->format('Y/m/d') }}
                    </td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-800 dark:text-gray-100">
                      {{ $item->quantity }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>

      {{-- 📝 メモ一覧 --}}
      <section class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-lg border-l-4 sm:border-l-8 border-blue-500 p-4 sm:p-6">
        <h3 class="text-lg sm:text-2xl font-bold text-blue-700 dark:text-blue-300 flex items-center gap-2 mb-3 sm:mb-4">
          💬 登録メモ一覧
        </h3>

        @if($memos->isEmpty())
          <p class="text-gray-500 dark:text-gray-300 text-center py-4 text-sm sm:text-lg">
            🗒️ 登録されたメモはありません。
          </p>
        @else
          <ul class="space-y-4 sm:space-y-5">
            @foreach($memos as $memo)
              <li class="p-4 sm:p-5 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-xl shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-center mb-2 text-sm sm:text-base">
                  <span class="font-semibold text-gray-800 dark:text-gray-200">
                    📦 商品：{{ $memo->item->item }}
                  </span>
                  <span class="text-gray-500 dark:text-gray-400">👤 {{ $memo->user->name }}</span>
                </div>
                <p class="text-gray-700 dark:text-gray-200 border-l-4 border-blue-500 pl-3 bg-white/60 dark:bg-gray-700/50 rounded-md py-2 text-sm sm:text-base">
                  {{ $memo->memo }}
                </p>
                <form action="{{ route('items.memos.destroy', [$memo->item_id, $memo->id]) }}" method="POST" class="mt-3 text-right" onsubmit="return confirm('このメモを削除しますか？');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-red-500 hover:text-red-600 text-xs sm:text-sm font-semibold">
                    🗑️ 削除
                  </button>
                </form>
              </li>
            @endforeach
          </ul>
        @endif
      </section>

    </div>
  </div>

  {{-- 🧩 Lucideアイコン初期化 --}}
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      if (window.lucide) lucide.createIcons();
    });
  </script>
</x-app-layout>

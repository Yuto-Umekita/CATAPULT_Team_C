<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-center text-gray-800 leading-tight">
            🏢 企業用通知
        </h2>
    </x-slot>
    

    {{-- 🔔 通知エリア --}}
    @if (!empty($notifications))
      <div class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-300 dark:border-indigo-600 rounded-2xl p-6 shadow-sm">
        <h3 class="text-xl font-semibold text-indigo-700 dark:text-indigo-300 mb-3 flex items-center gap-2">
          <i data-lucide="bell-ring" class="w-6 h-6"></i>
          通知センター
        </h3>

        {{-- 🔹 あなたの設定 --}}
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
          あなたの在庫閾値設定：<span class="font-semibold text-indigo-600 dark:text-indigo-300">{{ $threshold }}</span> 個未満
        </p>

        <ul class="list-disc list-inside text-gray-800 dark:text-gray-200 space-y-1">
          @foreach ($notifications as $note)
            <li>{{ $note }}</li>
          @endforeach
        </ul>
      </div>
    @else
      {{-- 通知がない場合 --}}
      <div class="bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-2xl p-6 text-center text-gray-600 dark:text-gray-400">
        現在通知はありません。
        <p class="mt-2 text-sm">あなたの在庫閾値設定：<span class="font-semibold text-indigo-600 dark:text-indigo-300">{{ $threshold }}</span> 個未満</p>
      </div>
    @endif

  <script>
    document.addEventListener("DOMContentLoaded", () => lucide.createIcons());
  </script>


    <div class="py-12 bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 min-h-screen">
        <div class="max-w-6xl mx-auto px-6 space-y-12">

            {{-- 📦 賞味期限切れ --}}
            <section class="bg-white rounded-2xl shadow-xl border-l-8 border-red-500">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-red-600 flex items-center gap-2 mb-4">
                        <span>🚨</span> 賞味期限切れの在庫
                    </h3>

                    @if($expiredItems->isEmpty())
                        <p class="text-gray-500 text-center py-6 text-lg">✨ 現在、期限切れの在庫はありません。</p>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-red-100">
                            <table class="w-full border-collapse">
                                <thead class="bg-red-50 text-red-700">
                                    <tr>
                                        <th class="px-4 py-3 text-center font-semibold">商品名</th>
                                        <th class="px-4 py-3 text-center font-semibold">賞味期限</th>
                                        <th class="px-4 py-3 text-center font-semibold">数量</th>
                                        <th class="px-4 py-3 text-center font-semibold">担当者</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiredItems as $item)
                                        <tr class="border-b hover:bg-red-50 transition">
                                            <td class="px-4 py-3 text-center font-medium text-red-700">{{ $item->item }}</td>
                                            <td class="px-4 py-3 text-center">{{ $item->expiration_date->format('Y/m/d') }}</td>
                                            <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $item->user->name ?? '―' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>

            {{-- ⏰ 期限間近 --}}
            <section class="bg-white rounded-2xl shadow-xl border-l-8 border-yellow-500">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-yellow-600 flex items-center gap-2 mb-4">
                        <span>⏳</span> 賞味期限が近い在庫（7日以内）
                    </h3>

                    @if($nearExpiredItems->isEmpty())
                        <p class="text-gray-500 text-center py-6 text-lg">✅ 期限間近の在庫はありません。</p>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-yellow-100">
                            <table class="w-full border-collapse">
                                <thead class="bg-yellow-50 text-yellow-700">
                                    <tr>
                                        <th class="px-4 py-3 text-center font-semibold">商品名</th>
                                        <th class="px-4 py-3 text-center font-semibold">賞味期限</th>
                                        <th class="px-4 py-3 text-center font-semibold">数量</th>
                                        <th class="px-4 py-3 text-center font-semibold">担当者</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nearExpiredItems as $item)
                                        <tr class="border-b hover:bg-yellow-50 transition">
                                            <td class="px-4 py-3 text-center">{{ $item->item }}</td>
                                            <td class="px-4 py-3 text-center">{{ $item->expiration_date->format('Y/m/d') }}</td>
                                            <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $item->user->name ?? '―' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>

            {{-- 📝 社内メモ一覧 --}}
            <section class="bg-white rounded-2xl shadow-xl border-l-8 border-blue-500">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-blue-700 flex items-center gap-2 mb-4">
                        <span>💬</span> 社内共有メモ
                    </h3>

                    @if($memos->isEmpty())
                        <p class="text-gray-500 text-center py-6 text-lg">🗒️ 登録された社内メモはありません。</p>
                    @else
                        <ul class="space-y-5">
                            @foreach($memos as $memo)
                                <li class="p-5 bg-blue-50 border border-blue-200 rounded-xl shadow-sm hover:shadow-md transition">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-semibold text-gray-800">
                                            📦 商品：{{ $memo->item->item }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            ✍️ 登録者：{{ $memo->user->name }}
                                        </span>
                                    </div>

                                    <p class="text-gray-700 text-base border-l-4 border-blue-500 pl-3 bg-white/70 rounded-md py-2">
                                        {{ $memo->memo }}
                                    </p>

                                    {{-- 削除ボタン --}}
                                    <form action="{{ route('items.memos.destroy', [$memo->item_id, $memo->id]) }}" 
                                          method="POST" 
                                          class="mt-3 text-right"
                                          onsubmit="return confirm('このメモを削除しますか？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-500 hover:text-red-600 text-sm font-semibold">
                                            🗑️ 削除
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>

            {{-- 📈 在庫サマリー --}}
            <section class="bg-white rounded-2xl shadow-xl border-l-8 border-indigo-500">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-indigo-700 flex items-center gap-2 mb-4">
                        <span>📊</span> 在庫サマリー
                    </h3>

                    <p class="text-gray-600 text-sm mb-4">※システム内に登録された全在庫の概要です。</p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="bg-indigo-50 rounded-xl p-5 text-center shadow-sm">
                            <p class="text-3xl font-bold text-indigo-700">{{ $totalItems ?? 0 }}</p>
                            <p class="text-gray-600 mt-1">総商品数</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-5 text-center shadow-sm">
                            <p class="text-3xl font-bold text-green-700">{{ $activeItems ?? 0 }}</p>
                            <p class="text-gray-600 mt-1">有効在庫</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-5 text-center shadow-sm">
                            <p class="text-3xl font-bold text-red-600">{{ $expiredItems->count() }}</p>
                            <p class="text-gray-600 mt-1">期限切れ在庫</p>
                        </div>
                    </div>
                </div>
            </section>

                </a>
            </div>
        </div>
    </div>
</x-app-layout>

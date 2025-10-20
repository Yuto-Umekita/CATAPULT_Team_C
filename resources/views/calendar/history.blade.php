<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-semibold text-center text-gray-800">
      🗂️ 入出庫履歴一覧
    </h2>
  </x-slot>

  <div class="max-w-6xl mx-auto py-8 px-4">
    <div class="bg-white shadow rounded-xl p-6">
      @if($completedEvents->isEmpty())
        <p class="text-gray-500 text-center">完了済みの入出庫はまだありません。</p>
      @else
        <table class="min-w-full border border-gray-200 text-sm">
          <thead class="bg-gray-100">
            <tr class="text-left">
              <th class="p-2 border-b">日付</th>
              <th class="p-2 border-b">種類</th>
              <th class="p-2 border-b">商品名</th>
              <th class="p-2 border-b">数量</th>
              <th class="p-2 border-b">メモ</th>
            </tr>
          </thead>
          <tbody>
            @foreach($completedEvents as $event)
              <tr class="hover:bg-gray-50">
                <td class="p-2 border-b">{{ $event->date->format('Y-m-d') }}</td>
                <td class="p-2 border-b">{{ $event->type }}</td>
                <td class="p-2 border-b">{{ $event->item->item ?? $event->item_name ?? '商品未指定' }}</td>
                <td class="p-2 border-b">{{ $event->quantity }}</td>
                <td class="p-2 border-b text-gray-500">{{ $event->notes ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>
</x-app-layout>

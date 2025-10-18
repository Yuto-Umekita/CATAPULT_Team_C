<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
      {{ __('在庫の詳細') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-2xl font-bold text-gray-800">{{ $item->item }}</p>
              <p class="text-gray-600 mt-2">個数: {{ $item->quantity }}</p>
              <p class="text-gray-600">
                賞味期限:
                @if ($item->expiration_date)
                  @if ($item->expiration_date->isPast())
                    <span class="text-red-600 font-bold">
                      {{ $item->expiration_date->format('Y/m/d') }}（期限切れ）
                    </span>
                  @else
                    {{ $item->expiration_date->format('Y/m/d') }}
                    （あと {{ ceil(now()->floatDiffInRealDays($item->expiration_date)) }} 日）
                  @endif
                @else
                  なし
                @endif
              </p>
              <p class="text-gray-500 text-sm mt-2">登録者: {{ $item->user->name }}</p>
            </div>
            <div class="flex items-center space-x-4">
              <a href="{{ route('items.edit', $item) }}" 
                 class="text-blue-600 hover:text-blue-800 font-semibold">編集</a>
              <form action="{{ route('items.destroy', $item) }}" method="POST" 
                    onsubmit="return confirm('本当に削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">削除</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h3 class="text-xl font-semibold mb-4 border-b pb-2">コメント</h3>

          <form method="POST" action="{{ route('items.memos.store', $item) }}" class="mb-6">
            @csrf
            <div class="mb-4">
              <label for="memo" class="sr-only">コメント内容</label>
              <textarea name="memo" id="memo" rows="3" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="コメントやメモを入力..." required></textarea>
              @error('memo')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>
            <div class="flex justify-end">
              <button type="submit" 
                      class="bg-[#4973B5] hover:bg-[#2C5BA5] text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition">
                コメントを追加
              </button>
            </div>
          </form>

          <div class="space-y-4">
            @forelse ($item->memos->sortByDesc('created_at') as $memo)
              <div class="border rounded-lg p-4 bg-gray-50">
                <p class="text-gray-800">{{ $memo->memo }}</p>
                <div class="text-right text-sm text-gray-500 mt-2">
                  <span>{{ $memo->user->name }}</span>
                  <span class="mx-1">|</span>
                  <span>{{ $memo->created_at->format('Y/m/d H:i') }}</span>
                </div>
              </div>
            @empty
              <p class="text-gray-500">まだコメントはありません。</p>
            @endforelse
          </div>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>
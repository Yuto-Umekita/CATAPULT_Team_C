<x-app-layout> 
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
      {{ __('在庫追加') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">

          {{-- メッセージ（購入リストから遷移したときに表示） --}}
          @if (session('from_purchase'))
            <div class="mb-4 p-3 bg-blue-100 text-blue-800 rounded">
              購入リストから追加しています。内容を確認してください。
            </div>
          @endif

          <form method="POST" action="{{ route('items.store') }}">
            @csrf

            <!-- 商品名 -->
            <div class="mb-4">
              <label for="item" class="block text-gray-800 text-sm font-bold mb-2">▼商品名</label>
              <input type="text" name="item" id="item"
                value="{{ old('item', session('prefill_item_name') ?? ($item ?? request('item'))) }}"
                class="w-1/4 shadow appearance-none border rounded-lg py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

              @error('item')
                <span class="text-red-500 text-xs italic">{{ $message }}</span>
              @enderror
            </div>    

            <!-- 個数 -->
            <div class="mb-4">
              <label for="quantity" class="block text-gray-800 text-sm font-bold mb-2">▼個数</label>
              <input type="number" name="quantity" id="quantity"
                value="{{ old('quantity', session('prefill_quantity')) }}"
                class="w-24 border rounded-lg py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">

            </div>

            <!-- 賞味期限 -->
            <div class="mb-4">
              <label class="block text-gray-800 text-sm font-bold mb-2">▼賞味期限</label>
              <div class="flex space-x-2">
                <input type="number" name="expiration_year" placeholder="年" min="2025" max="2100"
                       class="w-1/4 border rounded-lg py-2 px-3 text-gray-700 focus:outline-none">
                <input type="number" name="expiration_month" placeholder="月" min="1" max="12"
                       class="w-1/4 border rounded-lg py-2 px-3 text-gray-700 focus:outline-none">
                <input type="number" name="expiration_day" placeholder="日" min="1" max="31"
                       class="w-1/4 border rounded-lg py-2 px-3 text-gray-700 focus:outline-none">
              </div>
            </div>

            <!-- 追加ボタン -->
            <button 
              type="submit" 
              class="bg-[#4973B5] hover:bg-[#2C5BA5] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
              追加する
            </button>
          </form>

        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-white leading-tight">
      {{ __('在庫編集') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <a href="{{ route('items.show', $item) }}" class="text-blue-500 hover:text-blue-700 mr-2">詳細に戻る</a>
          <form method="POST" action="{{ route('items.update', $item) }}">
            @csrf
            @method('PUT')
              
          <!-- 商品名 -->
      <div class="mb-4">
        <label for="item" class="block text-gray-800 text-sm font-bold mb-2">▼商品名</label>
        <input type="text" name="item" id="item"
              value="{{ $item->item }}"
              class="w-1/4 shadow appearance-none border rounded-lg py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        @error('item')
          <span class="text-red-500 text-xs italic">{{ $message }}</span>
        @enderror
      </div>    


            <!-- 賞味期限-->
            <div class="mb-4">
              <label class="block text-gray-800 text-sm font-bold mb-2">▼賞味期限</label>
              <div class="flex space-x-2">
                <input type="number" name="expiration_year" placeholder="年" min="2025" max="2100"
                       value="{{ old('expiration_year', $expiration['year']) }}"
                       class="w-1/4 border rounded-lg py-2 px-3 text-gray-700 focus:outline-none">
                <input type="number" name="expiration_month" placeholder="月" min="1" max="12"
                       value="{{ old('expiration_month', $expiration['month']) }}"
                       class="w-1/4 border rounded-lg py-2 px-3 text-gray-700 focus:outline-none">
                <input type="number" name="expiration_day" placeholder="日" min="1" max="31"
                       value="{{ old('expiration_day', $expiration['day']) }}"
                       class="w-1/4 border rounded-lg py-2 px-3 text-gray-700 focus:outline-none">
              </div>


          <!-- 個数 -->
      <div class="mb-4">
        <label for="quantity" class="block text-gray-800 text-sm font-bold mb-2">▼個数</label>
        <input type="number" name="quantity" id="quantity"
              value="{{ old('quantity', $item->quantity) }}"
              min="1"
              class="w-1/4 shadow appearance-none border rounded-lg py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
      </div>
    </div>

          <button type="submit" class="bg-[#4973B5] text-white rounded-lg hover:bg-[#2C5BA5] font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">変更</button>
         </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
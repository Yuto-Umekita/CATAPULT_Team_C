<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-white leading-tight">
      🛒 購入予定品
    </h2>
  </x-slot>

  <script src="https://unpkg.com/alpinejs" defer></script>

  <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8"
       x-data="{ showToast: false, toastMessage: '' }"
       x-init="
         @if (session('success'))
           toastMessage = '{{ session('success') }}';
           showToast = true;
           setTimeout(() => showToast = false, 2500);
         @endif
       ">

    <!-- ✅ トースト通知 -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-400"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-6 right-6 z-50 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 
                shadow-lg rounded-lg px-6 py-3 text-gray-700 dark:text-gray-100 font-medium">
      <span x-text="toastMessage"></span>
    </div>

    <!-- 🧾 登録フォーム -->
    <form method="POST" action="{{ route('purchase_lists.store') }}"
          class="mb-8 bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <input type="text" name="item" placeholder="商品名" required
               class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                      rounded-lg px-3 py-2 w-full text-sm focus:ring focus:ring-blue-300 focus:outline-none">
        <input type="number" name="quantity" placeholder="個数（任意）" min="1"
               class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                      rounded-lg px-3 py-2 w-full text-sm focus:ring focus:ring-blue-300 focus:outline-none">
        <input type="date" name="purchase_date"
               class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100
                      rounded-lg px-3 py-2 w-full text-sm focus:ring focus:ring-blue-300 focus:outline-none">
      </div>
      <div class="text-center">
        <button type="submit"
                class="px-6 py-2 text-white font-semibold rounded-lg shadow-sm
                       bg-[#6B8FD9] hover:bg-[#527BCB]
                       transition-all duration-300 transform hover:scale-[1.02]">
          ➕ 追加
        </button>
      </div>
    </form>

    <!-- 📋 リスト一覧 -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
      @if($lists->isEmpty())
        <p class="text-center text-gray-700 dark:text-gray-300 text-lg font-medium">
          購入予定のものはありません。
        </p>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-center border-collapse text-sm sm:text-base">
            <thead>
              <tr class="bg-[#6B8FD9] dark:bg-[#527BCB] text-white">
                <th class="py-3 rounded-tl-lg">商品名</th>
                <th class="py-3">個数</th>
                <th class="py-3">購入予定日</th>
                <th class="py-3 rounded-tr-lg">操作</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              @foreach($lists as $list)
                <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-200">
                  <td class="py-3 font-semibold text-gray-800 dark:text-gray-100 break-words px-2">
                    {{ $list->item }}
                  </td>
                  <td class="py-3 text-gray-700 dark:text-gray-200">{{ $list->quantity ?? '-' }}</td>
                  <td class="py-3 text-gray-700 dark:text-gray-200">
                    {{ $list->purchase_date ? \Carbon\Carbon::parse($list->purchase_date)->format('Y/m/d') : '-' }}
                  </td>
                  <td class="py-3">
                    <div class="flex flex-wrap justify-center gap-2">
                      <!-- 在庫へ追加 -->
                      <a href="{{ route('items.create', [
                          'item' => $list->item,
                          'quantity' => $list->quantity,
                          'purchase_date' => $list->purchase_date
                      ]) }}"
                      class="px-3 py-1.5 text-xs sm:text-sm text-white font-semibold rounded-lg
                             bg-[#FFB347] hover:bg-[#FF9A3C]
                             transition-all duration-300 transform hover:scale-[1.03]">
                         在庫へ追加
                      </a>

                      <!-- 削除 -->
                      <form method="POST" action="{{ route('purchase_lists.destroy', $list->id) }}"
                            onsubmit="return confirm('本当に削除しますか？')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 text-xs sm:text-sm text-white font-semibold rounded-lg
                                       bg-[#EE2E48] hover:bg-[#D22B3E]
                                       transition-all duration-300 transform hover:scale-[1.03]">
                           削除
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</x-app-layout>

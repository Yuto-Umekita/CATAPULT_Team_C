<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight text-center">
      {{ __('在庫（ユーザー別ピン対応）') }}
    </h2>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- ピン留めセクション --}}
    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">ピン留め中</h3>

    <ul id="pinned-list" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 mb-8">
      @forelse($pinned as $ing)
        <li class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 flex items-center gap-3"
            data-id="{{ $ing->id }}"
            draggable="true">
          <button class="pin-btn text-yellow-500 text-xl" data-id="{{ $ing->id }}" title="ピン解除">★</button>
          <div class="flex-1">
            <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $ing->name }}</div>
            {{-- ここに数量など任意の属性を表示してOK --}}
          </div>
          <span class="handle cursor-grab select-none opacity-60" title="ドラッグで並び替え">≡</span>
        </li>
      @empty
        <li class="text-gray-500 dark:text-gray-400">ピン留めされている在庫はありません。</li>
      @endforelse
    </ul>

    {{-- その他一覧 --}}
    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">その他</h3>

    <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
      @forelse($others as $ing)
        <li class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 flex items-center gap-3">
          <button class="pin-btn text-gray-400 hover:text-yellow-500 text-xl"
                  data-id="{{ $ing->id }}" title="ピン留め">☆</button>
          <div class="flex-1">
            <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $ing->name }}</div>
          </div>
        </li>
      @empty
        <li class="text-gray-500 dark:text-gray-400">該当する在庫はありません。</li>
      @endforelse
    </ul>

    <div class="mt-6">
      {{ $others->links() }}
    </div>
  </div>

  {{-- ピン切替 & 並び替えスクリプト --}}
  <script>
  // ピンON/OFF
  document.querySelectorAll('.pin-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const id = btn.dataset.id;
      const url = @json(route('ingredients.togglePinUser', ['ingredient' => 'ING_ID']));
      const postUrl = url.replace('ING_ID', id);

      const res = await fetch(postUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': @json(csrf_token()),
          'Accept': 'application/json'
        }
      });

      if (res.ok) {
        location.reload();
      } else {
        alert('ピン切替に失敗しました');
      }
    });
  });

  // ドラッグ&ドロップで並び替え（ピン一覧のみ）
  (function () {
    const list = document.getElementById('pinned-list');
    if (!list) return;

    let draggingEl = null;

    const setDraggable = (li) => {
      li.addEventListener('dragstart', () => { draggingEl = li; li.classList.add('opacity-60'); });
      li.addEventListener('dragend',   () => { draggingEl = null; li.classList.remove('opacity-60'); });
      li.addEventListener('dragover', (e) => {
        e.preventDefault();
        const rect = li.getBoundingClientRect();
        const before = (e.clientY - rect.top) < rect.height / 2;
        list.insertBefore(draggingEl, before ? li : li.nextSibling);
      });
    };

    list.querySelectorAll('li[data-id]').forEach(setDraggable);

    list.addEventListener('drop', async () => {
      const orderedIds = [...list.querySelectorAll('li[data-id]')].map(li => li.dataset.id);

      const res = await fetch(@json(route('ingredients.reorderPinsUser')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': @json(csrf_token()),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ ordered_ids: orderedIds })
      });

      if (!res.ok) {
        alert('並び替えの保存に失敗しました');
      }
    });
  })();
  </script>
</x-app-layout>

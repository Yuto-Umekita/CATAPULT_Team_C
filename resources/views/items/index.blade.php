<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-3xl text-gray-800 leading-tight text-center">
      {{ __('在庫一覧') }}
    </h2>
  </x-slot>

  <script src="https://unpkg.com/alpinejs" defer></script>

  <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8"
       x-data="tagFilter()"
       x-init="init()">

    <div class="mb-8">
      <button 
        @click="searchOpen = !searchOpen"
        class="flex items-center justify-between w-full bg-white rounded-2xl shadow-md p-4 hover:shadow-lg transition-all duration-200">
        <span class="text-lg font-semibold text-gray-800 flex items-center gap-2">
          🔍 検索オプション
        </span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="2" stroke="currentColor"
             class="w-6 h-6 text-gray-600 transform transition-transform duration-300"
             :class="searchOpen ? 'rotate-180' : ''">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <form 
        x-show="searchOpen"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
        method="GET" 
        action="{{ route('items.index') }}" 
        class="mt-4 bg-gradient-to-br from-blue-50 to-indigo-100 shadow-inner rounded-2xl p-6 space-y-6 border border-indigo-100">

        {{-- 商品名 --}}
        <div>
          <label class="block text-sm font-semibold text-gray-800 mb-1">商品名</label>
          <input type="text" name="keyword" value="{{ request('keyword') }}"
            placeholder="例: 牛乳"
            class="border rounded-lg px-3 py-2 w-full shadow-sm focus:ring focus:ring-blue-200">
        </div>

        {{-- 在庫数 --}}
        <div class="border border-indigo-100 rounded-lg p-4 bg-white/70 shadow-sm">
          <h3 class="text-sm font-semibold text-gray-800 mb-2">📦 在庫数</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-600">最小数（以上）</label>
              <input type="number" name="stock_min" value="{{ request('stock_min') }}"
                placeholder="0"
                class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200">
            </div>
            <div>
              <label class="block text-xs text-gray-600">最大数（以下）</label>
              <input type="number" name="stock_max" value="{{ request('stock_max') }}"
                placeholder="100"
                class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200">
            </div>
          </div>
        </div>

        {{-- 更新日 --}}
        <div class="border border-indigo-100 rounded-lg p-4 bg-white/70 shadow-sm">
          <h3 class="text-sm font-semibold text-gray-800 mb-2">🗓️ 更新日</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-600">開始日（以降）</label>
              <input type="date" name="updated_from" value="{{ request('updated_from') }}"
                class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200">
            </div>
            <div>
              <label class="block text-xs text-gray-600">終了日（以前）</label>
              <input type="date" name="updated_to" value="{{ request('updated_to') }}"
                class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200">
            </div>
          </div>
        </div>

        {{-- 賞味期限 --}}
        <div class="border border-indigo-100 rounded-lg p-4 bg-white/70 shadow-sm">
          <h3 class="text-sm font-semibold text-gray-800 mb-2">⏰ 賞味期限</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-600">開始日（以降）</label>
              <input type="date" name="expiration_from" value="{{ request('expiration_from') }}"
                class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200">
            </div>
            <div>
              <label class="block text-xs text-gray-600">終了日（以前）</label>
              <input type="date" name="expiration_to" value="{{ request('expiration_to') }}"
                class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-indigo-200">
            </div>
          </div>
        </div>

        {{-- ボタン --}}
        <div class="flex justify-between items-center mt-6">
          <div class="flex gap-3">
            <button type="submit"
              class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-md">
              検索
            </button>
            <a href="{{ route('items.index') }}"
              class="px-6 py-2 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400 transition shadow-md">
              リセット
            </a>
          </div>

            <a href="{{ route('recipes.index') }}" 
               class="relative px-6 py-2 text-white text-sm font-semibold rounded-xl 
                      bg-gradient-to-r from-[#FFB347] to-[#FF9A3C]
                        hover:from-[#4973B5] hover:to-[#335C9E]
                        backdrop-blur-md border border-white/30 shadow-lg
                        ring-2 ring-[#ffffff40] hover:ring-[#4973B5]/40
                        hover:shadow-[0_6px_18px_rgba(73,115,181,0.4)]
                          transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-[1.04]">
                在庫で作れる料理を表示
            </a>


        </div>
      </form>
    </div>

    <div class="mb-8 bg-white shadow-md rounded-2xl p-4">
      <div class="flex items-center flex-wrap gap-2 mb-3">
        <template x-for="tag in tags" :key="tag.id">
          <button
            type="button"
            class="px-3 py-1 rounded-full border text-sm transition-all duration-300"
            :class="selectedTags.includes(tag.id)
              ? 'bg-indigo-600 text-white border-indigo-600'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            x-text="tag.name"
            @click="toggleTagFilter(tag.id)"
            @contextmenu.prevent="openTagContextMenu($event, tag)">
          </button>
        </template>

        <button type="button"
                class="px-3 py-1 rounded-full border text-sm bg-indigo-600 text-white hover:bg-indigo-700"
                @click="openCreateModal()">＋</button>
      </div>
      <p class="text-sm text-gray-500">タグをクリックして在庫を絞り込みできます（複数選択可）</p>
    </div>

    <div class="bg-gradient-to-br from-indigo-100 to-blue-100 shadow-inner sm:rounded-2xl p-6">
      <template x-if="filteredItems.length === 0">
        <p class="text-center text-gray-600">該当する在庫がありません。</p>
      </template>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="item in filteredItems" :key="item.fade_key">
          <div
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="p-6 bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 flex flex-col">

            <div class="flex-grow">
              <p class="text-xl font-semibold mb-2 text-gray-800" x-text="item.item"></p>

              <div class="flex flex-wrap gap-1 mb-2">
                <template x-for="t in item.tags" :key="t.id">
                  <span class="px-2 py-1 text-xs bg-gray-100 border rounded-full cursor-pointer hover:bg-gray-200"
                        x-text="t.name"
                        @contextmenu.prevent="openTagContextMenu($event, t, item.id)">
                  </span>
                </template>
                <button class="px-2 py-1 text-xs bg-indigo-500 text-white rounded-full hover:bg-indigo-600"
                        @click="openItemTagModal(item.id)">
                  ＋
                </button>
              </div>

              <p class="text-gray-800 text-sm mt-2">
                賞味期限：
                <template x-if="item.expiration_date">
                  <span x-text="formatExpiration(item.expiration_date)"
                        :class="isExpired(item.expiration_date) ? 'text-[#EE2E48] font-bold' : ''"></span>
                </template>
                <template x-if="!item.expiration_date"><span>なし</span></template>
              </p>

              <p class="text-gray-800 text-sm">個数：<span x-text="item.quantity"></span></p>
              <p class="text-gray-600 text-xs mb-2">登録者：<span x-text="item.user.name"></span></p>

              <div class="mt-4 border-t pt-3" x-show="item.memos.length > 0">
                <p class="text-xs text-gray-500 font-semibold mb-1 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                  最新のコメント:
                </p>
                <div class="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg">
                  <p x-text="item.memos[0].memo.substring(0, 50) + (item.memos[0].memo.length > 50 ? '...' : '')" class="break-words"></p>
                  <p class="text-xs text-gray-400 text-right mt-1" x-text="`- ${item.memos[0].user.name}`"></p>
                </div>
              </div>
              </div>

            <a :href="`/items/${item.id}`" 
               class="block text-right text-[#4973B5] hover:text-[#2C5BA5] font-medium mt-4 self-end">
              詳細 →
            </a>
          </div>
        </template>
      </div>
    </div>
  </div>

  {{-- ✅ Alpine.jsロジック --}}
  @push('scripts')
  <script>
  function tagFilter() {
    return {
      searchOpen: false, // 検索フォーム開閉状態
      tags: [],
      items: [],
      filteredItems: [],
      selectedTags: [],
      createModal: false,
      newTagName: '',
      error: '',
      contextMenu: { show: false, x: 0, y: 0, target: null, itemId: null },
      itemTagModal: { show: false, itemId: null, name: '', error: '' },

      async init() {
        await this.fetchTags();
        await this.fetchItems();
      },

      async fetchTags() {
        const res = await fetch(`{{ route('tags.index') }}`);
        this.tags = await res.json();
      },

      async fetchItems() {
        const url = new URL(`{{ route('items.index') }}`);
        url.searchParams.set('json', '1');
        @foreach (['keyword','stock_min','stock_max','updated_from','updated_to','expiration_from','expiration_to'] as $param)
          @if (request($param))
            url.searchParams.set('{{ $param }}', '{{ request($param) }}');
          @endif
        @endforeach
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        this.items = await res.json();
        this.filteredItems = this.items.map(i => ({ ...i, fade_key: Math.random() }));
      },

      toggleTagFilter(tagId) {
        if (this.selectedTags.includes(tagId)) {
          this.selectedTags = this.selectedTags.filter(id => id !== tagId);
        } else {
          this.selectedTags.push(tagId);
        }
        this.applyFilter();
      },

      applyFilter() {
        if (this.selectedTags.length === 0) {
          this.filteredItems = this.items.map(i => ({ ...i, fade_key: Math.random() }));
          return;
        }
        const selected = this.selectedTags.map(Number);
        const filtered = this.items.filter(item =>
          item.tags.some(tag => selected.includes(Number(tag.id)))
        );
        this.filteredItems = filtered.map(i => ({ ...i, fade_key: Math.random() }));
      },

      openCreateModal() {
        this.newTagName = '';
        this.error = '';
        this.createModal = true;
      },

      async createTag() {
        const res = await fetch(`{{ route('tags.store') }}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ name: this.newTagName.trim() }),
        });
        if (res.ok) {
          this.createModal = false;
          await this.fetchTags();
        } else {
          this.error = '作成に失敗しました';
        }
      },

      openTagContextMenu(ev, tag, itemId = null) {
        ev.preventDefault();
        this.contextMenu = { show: true, x: ev.pageX, y: ev.pageY, target: tag, itemId: itemId };
      },

      async openEditTag() {
        if (!this.contextMenu.target) return;
        const newName = prompt("新しいタグ名を入力してください", this.contextMenu.target.name);
        if (!newName || newName.trim() === this.contextMenu.target.name) {
          this.contextMenu.show = false;
          return;
        }
        const res = await fetch(`{{ url('/tags') }}/${this.contextMenu.target.id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ name: newName.trim() }),
        });
        this.contextMenu.show = false;
        if (res.ok) {
          await this.fetchTags();
          await this.fetchItems();
        } else {
          alert('タグの編集に失敗しました');
        }
      },

      async confirmDeleteTag() {
        if (!this.contextMenu.target) return;
        if (!confirm(`「${this.contextMenu.target.name}」を削除しますか？`)) return;
        const res = await fetch(`{{ url('/tags') }}/${this.contextMenu.target.id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        });
        this.contextMenu.show = false;
        if (res.ok) {
          await this.fetchTags();
          await this.fetchItems();
        } else {
          alert('タグの削除に失敗しました');
        }
      },

      openItemTagModal(id) {
        this.itemTagModal = { show: true, itemId: id, name: '', error: '' };
      },

      async addTagToItem() {
        const name = this.itemTagModal.name.trim();
        if (!name) {
          this.itemTagModal.error = 'タグ名を入力してください';
          return;
        }
        const res = await fetch(`{{ route('tags.store') }}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({ name: name, item_id: this.itemTagModal.itemId }),
        });

        if (res.ok) {
          this.itemTagModal.show = false;
          await this.fetchItems();
        } else {
          this.itemTagModal.error = '追加に失敗しました';
        }
      },

      formatExpiration(dateStr) {
        if (!dateStr) return 'なし';
        const date = new Date(dateStr);
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        const now = new Date();
        const diff = Math.ceil((date - now) / (1000 * 60 * 60 * 24));
        return diff < 0 ? `${y}/${m}/${d}（期限切れ）` : `${y}/${m}/${d}（あと ${diff} 日）`;
      },

      isExpired(dateStr) {
        if (!dateStr) return false;
        return new Date(dateStr) < new Date();
      },
    };
  }
  </script>
  @endpush
</x-app-layout>

@stack('scripts')
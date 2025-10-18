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

    {{-- 🔍 検索オプション --}}
    {{-- （中略：検索フォーム部は変更なし） --}}

    {{-- 🏷️ タグ一覧 --}}
    {{-- （中略：タグ一覧部は変更なし） --}}

    {{-- 📦 在庫カード一覧 --}}
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
            class="relative p-6 bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 flex flex-col">

            <!-- ✅ 新しいピンアイコン（pushpin風デザイン・大サイズ） -->
            <button 
              @click.prevent="togglePin(item)" 
              class="absolute top-3 right-3 transition-transform duration-200 hover:scale-110 active:scale-95">
              <svg xmlns="http://www.w3.org/2000/svg" 
                   viewBox="0 0 24 24"
                   fill="currentColor"
                   :class="item.pinned ? 'text-yellow-400 drop-shadow-md' : 'text-gray-400 hover:text-yellow-400 drop-shadow-sm'"
                   class="w-8 h-8 transition-colors duration-300">
                <path fill-rule="evenodd" d="M15.22 2.97a.75.75 0 0 1 1.06 0l4.75 4.75a.75.75 0 0 1 0 1.06l-2.47 2.47a.75.75 0 0 1-.53.22h-.19l-.63.63a4.5 4.5 0 0 1-4.76 1.07l-1.47 1.47 5.5 5.5a.75.75 0 0 1-1.06 1.06l-5.5-5.5-1.47 1.47a4.5 4.5 0 0 1-1.07 4.76l-.63.63v.19a.75.75 0 0 1-.22.53l-2.47 2.47a.75.75 0 0 1-1.06 0l-4.75-4.75a.75.75 0 0 1 0-1.06l2.47-2.47a.75.75 0 0 1 .53-.22h.19l.63-.63a4.5 4.5 0 0 1 4.76-1.07l1.47-1.47-5.5-5.5a.75.75 0 1 1 1.06-1.06l5.5 5.5 1.47-1.47a4.5 4.5 0 0 1 1.07-4.76l.63-.63v-.19a.75.75 0 0 1 .22-.53l2.47-2.47Z" clip-rule="evenodd"/>
              </svg>
            </button>

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
                  💬 最新のコメント:
                </p>
                <div class="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg">
                  <p x-text="item.memos[0].memo.substring(0, 50) + (item.memos[0].memo.length > 50 ? '...' : '')"
                     class="break-words"></p>
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
      searchOpen: false,
      tags: [],
      items: [],
      filteredItems: [],
      selectedTags: [],
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
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        this.items = await res.json();
        this.filteredItems = this.items.map(i => ({ ...i, fade_key: Math.random() }));
      },

      async togglePin(item) {
        try {
          const res = await fetch(`/items/${item.id}/pin`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
          });
          const data = await res.json();
          item.pinned = data.pinned;
        } catch (e) {
          alert('ピンの更新に失敗しました');
          console.error(e);
        }
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
        return new Date(dateStr) < new Date();
      },
    };
  }
  </script>
  @endpush
</x-app-layout>

@stack('scripts')

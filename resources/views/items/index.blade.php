<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-3xl text-gray-800 leading-tight text-center">
      {{ __('在庫一覧') }}
    </h2>
  </x-slot>

  <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8"
       x-data="tagFilter()"
       x-init="init()">

    {{-- 🔍 検索オプション --}}
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
        x-transition:enter="transition ease-out duration-300"
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

    {{-- 🏷️ タグ一覧 --}}
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
            @contextmenu.stop.prevent="openTagContextMenu($event, tag)">
          </button>
        </template>

        <button type="button"
                class="px-3 py-1 rounded-full border text-sm bg-indigo-600 text-white hover:bg-indigo-700"
                @click="openCreateModal()">＋</button>
      </div>
      <p class="text-sm text-gray-500">タグをクリックして在庫を絞り込みできます（複数選択可）</p>
    </div>
    

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




    {{-- ================================================
 🖱️ タグ右クリックメニュー（美しいアイコン＆カラー付き）
================================================ --}}
<div 
  x-show="contextMenu.show"
  x-cloak
  @click.away="contextMenu.show = false"
  class="fixed z-50 bg-white/95 backdrop-blur-md border border-gray-200 shadow-2xl rounded-xl w-48 overflow-hidden transform transition-all duration-200"
  :style="`top: ${contextMenu.y}px; left: ${contextMenu.x}px;`"
  x-transition.origin-top-left
  x-transition:enter="transition ease-out duration-200"
  x-transition:enter-start="opacity-0 scale-95"
  x-transition:enter-end="opacity-100 scale-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100 scale-100"
  x-transition:leave-end="opacity-0 scale-95"
>
  <ul class="divide-y divide-gray-100">
    {{-- ✏️ 編集ボタン --}}
    <li>
      <button 
        @click="openEditTag"
        class="group flex items-center gap-2 w-full text-left px-4 py-3 
               text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 
               transition-colors duration-150">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-5 h-5 text-gray-500 group-hover:text-indigo-500 transition"
             fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" 
                d="M16.862 3.487a2.25 2.25 0 013.182 3.182L8.25 18.563l-4.5.75.75-4.5L16.862 3.487z" />
        </svg>
        <span class="font-medium">編集する</span>
      </button>
    </li>

    {{-- 🗑️ 削除ボタン --}}
    <li>
      <button 
        @click="confirmDeleteTag"
        class="group flex items-center gap-2 w-full text-left px-4 py-3 
               text-red-600 hover:bg-red-50 hover:text-red-700 
               transition-colors duration-150">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-5 h-5 text-red-500 group-hover:text-red-600 transition"
             fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" 
                d="M6 18L18 6M6 6l12 12" />
        </svg>
        <span class="font-medium">削除する</span>
      </button>
    </li>
  </ul>
</div>
  </div>



{{-- ✅ Alpine.jsロジック --}}
@push('scripts')
<script>
function tagFilter() {
  return {
    // -------------------------------
    // 🔧 初期データ
    // -------------------------------
    searchOpen: false,
    tags: [],
    items: [],
    filteredItems: [],
    selectedTags: [],
    createModal: false,
    newTagName: '',
    error: '',
    contextMenu: { show: false, x: 0, y: 0, target: null, itemId: null },
    itemTagModal: { show: false, itemId: null, name: '', error: '' },

    // -------------------------------
    // 🏁 初期化処理
    // -------------------------------
    async init() {
      await this.fetchTags();
      await this.fetchItems();
    },

    // -------------------------------
    // 🏷 タグ一覧取得
    // -------------------------------
    async fetchTags() {
      try {
        const res = await fetch(`{{ route('tags.index') }}`);
        if (!res.ok) throw new Error('タグ取得失敗');
        this.tags = await res.json();
      } catch (e) {
        console.error(e);
      }
    },

    // -------------------------------
    // 📦 アイテム一覧取得
    // -------------------------------
    async fetchItems() {
      try {
        const res = await fetch(`{{ route('items.index') }}`, {
          headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('アイテム取得失敗');
        this.items = await res.json();
        this.filteredItems = this.items.map(i => ({ ...i, fade_key: i.id }));
      } catch (e) {
        console.error(e);
      }
    },

    // -------------------------------
    // ➕ タグ作成（全体 or 商品別）
    // -------------------------------
    async createTag() {
      const payload = { name: this.newTagName.trim() };
      if (this.itemTagModal.itemId) payload.item_id = this.itemTagModal.itemId;

      try {
        const res = await fetch(`{{ route('tags.store') }}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify(payload)
        });

        const data = await res.json();
        if (!res.ok || !data.success) throw new Error('作成に失敗しました');

        this.createModal = false;
        this.itemTagModal.show = false;
        await this.fetchTags();
        await this.fetchItems();
      } catch (e) {
        console.error(e);
        this.error = e.message;
      }
    },

    // -------------------------------
    // ✏️ タグ編集
    // -------------------------------
    async openEditTag() {
      if (!this.contextMenu.target) return;
      const newName = prompt("新しいタグ名を入力してください", this.contextMenu.target.name);
      if (!newName || newName.trim() === this.contextMenu.target.name) {
        this.contextMenu.show = false;
        return;
      }

      try {
        const res = await fetch(`/tags/${this.contextMenu.target.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ name: newName.trim() })
        });

        const data = await res.json();
        if (!res.ok || !data.success) throw new Error('タグの更新に失敗');

        this.contextMenu.show = false;
        await this.fetchTags();
        await this.fetchItems();
      } catch (e) {
        console.error(e);
        alert(e.message);
      }
    },

    // -------------------------------
    // 🗑️ タグ削除
    // -------------------------------
    async confirmDeleteTag() {
      if (!this.contextMenu.target) return;
      if (!confirm(`「${this.contextMenu.target.name}」を削除しますか？`)) return;

      try {
        const res = await fetch(`/tags/${this.contextMenu.target.id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        const data = await res.json();
        if (!res.ok || !data.success) throw new Error('削除に失敗');

        this.contextMenu.show = false;
        await this.fetchTags();
        await this.fetchItems();
      } catch (e) {
        console.error(e);
        alert(e.message);
      }
    },

    // -------------------------------
    // 🧭 タグ絞り込み
    // -------------------------------
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

    // -------------------------------
    // 📌 ピン機能
    // -------------------------------
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

    // -------------------------------
    // ⚙️ タグ作成モーダル
    // -------------------------------
    openCreateModal() {
      this.newTagName = '';
      this.error = '';
      this.createModal = true;
    },

    // -------------------------------
    // 🧩 コンテキストメニュー
    // -------------------------------
    openTagContextMenu(ev, tag, itemId = null) {
      ev.preventDefault();
      this.contextMenu = { show: true, x: ev.pageX, y: ev.pageY, target: tag, itemId: itemId };
      console.log("右クリックしたタグ:", this.contextMenu.target);
    },

    // -------------------------------
    // 🏷️ 商品タグ追加モーダル
    // -------------------------------
    openItemTagModal(id) {
      this.itemTagModal = { show: true, itemId: id, name: '', error: '' };
    },

    // -------------------------------
    // 🏷️ 商品タグ追加処理
    // -------------------------------
    async addTagToItem() {
      const name = this.itemTagModal.name.trim();
      if (!name) {
        this.itemTagModal.error = 'タグ名を入力してください';
        return;
      }

      try {
        const res = await fetch(`{{ route('tags.store') }}`, {
          method: 'POST',
          headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
          },
          body: JSON.stringify({ name: name, item_id: this.itemTagModal.itemId }),
        });

        const data = await res.json();
        if (!res.ok || !data.success) throw new Error('追加に失敗しました');

        this.itemTagModal.show = false;
        await this.fetchItems();
      } catch (e) {
        this.itemTagModal.error = e.message;
        console.error(e);
      }
    },

    // -------------------------------
    // ⏰ 賞味期限関連
    // -------------------------------
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

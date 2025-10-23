<x-app-layout>
  <x-slot name="header">
      <div class="flex justify-between items-center w-full relative">
          
          {{-- 🔙 左上の戻るボタン --}}
          <a href="{{ route('mode.select') }}"
             class="flex items-center gap-1 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
              <i data-lucide="arrow-left" class="w-5 h-5"></i>
              <span class="text-sm sm:text-base font-medium">モード選択に戻る</span>
          </a>

          {{-- 📘 中央タイトル --}}
          <h2 class="flex-1 text-center font-semibold text-2xl sm:text-3xl text-gray-800 dark:text-gray-100 leading-tight">
              グループを選択
          </h2>

          {{-- 🪶 右のダミースペース（バランス用） --}}
          <div class="w-[140px] sm:w-[160px]"></div>
      </div>
  </x-slot>

  <div class="max-w-3xl mx-auto mt-10 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 transition-all duration-300"
       x-data="groupSelect({{ json_encode($groups) }}, '{{ $mode }}')">

      {{-- 💬 メッセージ --}}
      <template x-if="message">
          <div x-text="message"
              class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-center">
          </div>
      </template>

      {{-- ✅ フラッシュメッセージ（サーバー側） --}}
      @if(session('info'))
          <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-center">
              {{ session('info') }}
          </div>
      @endif
      @if(session('success'))
          <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-center">
              {{ session('success') }}
          </div>
      @endif

      <p class="text-gray-600 dark:text-gray-300 mb-8 text-center text-lg leading-relaxed">
          管理・操作するグループを選択してください。<br>
          必要に応じて新しいグループも作成できます。
      </p>

      {{-- 📋 グループ選択フォーム --}}
      <form method="POST" action="{{ route('group.set') }}">
          @csrf

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10">
              <template x-for="group in groups" :key="group.id">
                  <label class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 flex flex-col items-center cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 transition shadow-sm hover:shadow-md">
                      <input type="radio" name="group_id" :value="group.id" class="mb-3 accent-blue-500 scale-110" required>
                      <span class="font-bold text-lg text-gray-800 dark:text-gray-100" x-text="group.name"></span>
                      <span class="text-gray-500 dark:text-gray-400 text-sm mt-1" x-text="group.mode === 'household' ? '🏠 家庭用' : '🏢 企業用'"></span>
                  </label>
              </template>
          </div>

          <div class="flex flex-col sm:flex-row justify-center gap-4">
              <button type="button"
                  @click="openModal = true"
                  class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition transform hover:scale-105">
                  ➕ 新しいグループを作成
              </button>

              <button type="submit"
                  class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl shadow-md transition transform hover:scale-105 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                  ✅ このグループを選択
              </button>
          </div>
      </form>

      {{-- 🪄 グループ作成モーダル --}}
      <div x-show="openModal"
           class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
           x-cloak>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6 relative"
               @click.outside="openModal = false">
              <h3 class="text-xl font-semibold mb-4 text-center text-gray-800 dark:text-gray-100">新しいグループを作成</h3>

              <form @submit.prevent="createGroup">
                  <div class="mb-4">
                      <label class="block text-gray-700 dark:text-gray-300 mb-1">グループ名</label>
                      <input type="text" x-model="newGroupName"
                             class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                             placeholder="例：チームA" required>
                  </div>

                  <div class="mb-6">
                      <label class="block text-gray-700 dark:text-gray-300 mb-1">モード</label>
                      <input type="text" x-model="modeDisplay" disabled
                             class="w-full border rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                  </div>

                  <div class="flex justify-center gap-3">
                      <button type="button"
                          @click="openModal = false"
                          class="px-5 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                          キャンセル
                      </button>
                      <button type="submit"
                          class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                          作成
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>

  {{-- Alpine.js ロジック --}}
  @push('scripts')
      <script>
          function groupSelect(initialGroups, mode) {
              return {
                  groups: initialGroups,
                  mode: mode,
                  newGroupName: '',
                  openModal: false,
                  message: '',
                  get modeDisplay() {
                      return this.mode === 'household' ? '家庭用' : '企業用';
                  },
                  async createGroup() {
                      if (!this.newGroupName.trim()) return;
                      try {
                          const res = await fetch("{{ route('groups.store') }}", {
                              method: "POST",
                              headers: {
                                  "Content-Type": "application/json",
                                  "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content,
                                  "Accept": "application/json"
                              },
                              body: JSON.stringify({
                                  name: this.newGroupName,
                                  mode: this.mode
                              })
                          });

                          const data = await res.json();

                          if (data.success && data.group) {
                              this.groups.push(data.group);
                              this.message = `グループ「${data.group.name}」を作成しました。`;
                              this.openModal = false;
                              this.newGroupName = '';
                              setTimeout(() => this.message = '', 4000);
                          } else {
                              alert('グループ作成に失敗しました。');
                          }
                      } catch (err) {
                          console.error(err);
                          alert('通信エラーが発生しました。');
                      }
                  }
              }
          }

          document.addEventListener("DOMContentLoaded", () => {
              if (window.lucide) lucide.createIcons();
          });
      </script>
  @endpush
</x-app-layout>

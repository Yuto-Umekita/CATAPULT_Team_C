<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-gray-100 leading-tight">
      📅 入出庫スケジュール管理
    </h2>
  </x-slot>

  <div x-data="calendarApp()" x-init="init()"
       class="flex flex-col md:flex-row gap-6 max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6 
              bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    {{-- 🗓️ カレンダー --}}
    <div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow p-3 sm:p-4 border border-gray-200 dark:border-gray-700">
      <div id="calendar"></div>
    </div>

    {{-- 📋 選択中の日の予定 --}}
    <div class="relative w-full md:w-80 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
      <h3 class="text-base sm:text-lg font-bold text-gray-700 dark:text-gray-100 mb-3 flex items-center gap-2 border-b border-gray-200 dark:border-gray-600 pb-2">
        📋 <span x-text="selectedLabel"></span>
      </h3>

      <template x-if="events.length === 0">
        <p class="text-gray-500 dark:text-gray-400 text-sm text-center">この日の予定はありません。</p>
      </template>

      <ul class="space-y-3 pb-16" x-show="events.length > 0">
        <template x-for="event in events" :key="event.id">
          <li class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-3 border-l-4"
              :class="event.type === '入庫' ? 'border-green-400' : 'border-blue-500'">
            <div class="flex justify-between items-center mb-1">
              <span class="font-semibold text-gray-800 dark:text-gray-100 text-sm"
                    x-text="`${event.type}：${event.name}`"></span>
              <span class="text-xs text-gray-500 dark:text-gray-300" x-text="`${event.quantity} 個`"></span>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-300 mb-2" x-text="event.notes || '（メモなし）'"></p>

            <template x-if="event.status !== '完了'">
              <div class="flex justify-center gap-2 mt-2">
                <form :action="`/calendar/events/${event.id}/complete`" method="POST">
                  @csrf
                  <button type="submit"
                          class="text-xs px-3 py-2 rounded-md bg-green-500 text-white hover:bg-green-600 transition shadow-sm">
                    ✅ 完了
                  </button>
                </form>
                <button @click="openDeleteModal(event.id, event.name)"
                        class="text-xs px-3 py-2 rounded-md bg-red-500 text-white hover:bg-red-600 transition shadow-sm">
                  🗑 削除
                </button>
              </div>
            </template>

            <template x-if="event.status === '完了'">
              <p class="text-xs text-green-500 font-semibold text-center">完了済み</p>
            </template>
          </li>
        </template>
      </ul>

      {{-- ➕ 予定追加ボタン --}}
      <button id="addEventBtn"
              class="absolute bottom-4 right-4 bg-blue-500 hover:bg-blue-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm shadow transition">
        ＋ 予定追加
      </button>
    </div>

    {{-- 🌟 予定追加モーダル --}}
    <div id="eventModal" class="fixed inset-0 bg-black/60 flex items-center justify-center hidden z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-5 w-80 sm:w-96 animate-fade-in text-gray-800 dark:text-gray-100">
        <h3 class="text-lg sm:text-xl font-bold mb-4 text-center">📅 新しい予定を追加</h3>

        <div class="space-y-3">
          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">種別</label>
            <select id="eventType" class="w-full border rounded p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
              <option value="入庫">入庫</option>
              <option value="出庫">出庫</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">商品名</label>
            <input id="eventItem" type="text" class="w-full border rounded p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" placeholder="例：牛乳">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">数量</label>
            <input id="eventQuantity" type="number" min="1" class="w-full border rounded p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" placeholder="例：5">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">メモ</label>
            <textarea id="eventNotes" rows="2" class="w-full border rounded p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" placeholder="任意メモ"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-5">
          <button id="cancelEvent" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 transition">キャンセル</button>
          <button id="saveEvent" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition">保存</button>
        </div>
      </div>
    </div>

    {{-- 🗑 削除確認モーダル（✅ x-data スコープ内に配置） --}}
    <div id="deleteModal" class="fixed inset-0 bg-black/60 flex items-center justify-center hidden z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-72 sm:w-80 animate-fade-in text-gray-800 dark:text-gray-100">
        <h3 class="text-base sm:text-lg font-bold mb-3 text-center">🗑 予定を削除しますか？</h3>
        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 text-center" 
           x-text="`「${deleteTargetName}」を削除してもよろしいですか？`"></p>
        <div class="flex justify-center gap-3">
          <button id="cancelDelete" class="px-3 py-2 rounded bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-sm">キャンセル</button>
          <button id="confirmDelete" class="px-3 py-2 rounded bg-red-600 hover:bg-red-700 text-white text-sm">削除</button>
        </div>
      </div>
    </div>
  </div>

  {{-- FullCalendar --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>

  <style>
    @keyframes fade-in {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in {
      animation: fade-in 0.2s ease-out;
    }
  </style>
<script>
  function calendarApp() {
    return {
      selectedDate: new Date().toISOString().slice(0, 10),
      selectedLabel: '今日の予定',
      events: [],
      addModal: null,
      deleteModal: null,
      deleteTargetId: null,
      deleteTargetName: '',
      isSaving: false,
      calendarInstance: null,

      async fetchEvents(date) {
        const res = await fetch(`{{ route('calendar.byDate') }}?date=${date}&_=${Date.now()}`, {
          credentials: 'same-origin' // ✅ Cookieを送信
        });
        this.events = res.ok ? await res.json() : [];
      },


      openDeleteModal(id, name) {
        this.deleteTargetId = id;
        this.deleteTargetName = name;
        this.deleteModal.classList.remove('hidden');
      },

      async confirmDelete() {
        const res = await fetch(`/calendar/events/${this.deleteTargetId}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (res.ok) {
          this.deleteModal.classList.add('hidden');
          await this.fetchEvents(this.selectedDate);
          if (this.calendarInstance) {
            this.calendarInstance.refetchEvents();
            setTimeout(() => this.calendarInstance.rerenderEvents(), 200);
          }
        }
      },

      async init() {
        this.addModal = document.getElementById('eventModal');
        this.deleteModal = document.getElementById('deleteModal');

        const addBtn = document.getElementById('addEventBtn');
        const cancelBtn = document.getElementById('cancelEvent');
        const saveBtn = document.getElementById('saveEvent');
        const cancelDelete = document.getElementById('cancelDelete');
        const confirmDelete = document.getElementById('confirmDelete');

        cancelDelete.addEventListener('click', () => this.deleteModal.classList.add('hidden'));
        confirmDelete.addEventListener('click', () => this.confirmDelete());

        await this.fetchEvents(this.selectedDate);

        // ✅ FullCalendar 初期化
        const calendarEl = document.getElementById('calendar');
        this.calendarInstance = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          locale: 'ja',
          height: 'auto',
          selectable: true,
          eventSources: [
            {
              url: '{{ route('calendar.fetch') }}',
              method: 'GET',
              extraParams: () => ({ _: Date.now() }),
              extraFetchOptions: { credentials: 'same-origin' } // ✅ Cookieを送信
            }
          ],

          dateClick: async (info) => {
            this.selectedDate = info.dateStr;
            const today = new Date().toISOString().slice(0, 10);
            this.selectedLabel = (this.selectedDate === today)
              ? '今日の予定'
              : `${this.selectedDate} の予定`;
            await this.fetchEvents(this.selectedDate);
          },
          eventDidMount: (info) => {
            const title = info.event.title || '';
            if (title.startsWith('入庫')) {
              info.el.style.backgroundColor = '#4ade80';
              info.el.style.borderColor = '#4ade80';
              info.el.style.color = '#1f2937';
            } else if (title.startsWith('出庫')) {
              info.el.style.backgroundColor = '#3b82f6';
              info.el.style.borderColor = '#3b82f6';
              info.el.style.color = '#ffffff';
            }
          }
        });

        this.calendarInstance.render();

        // ✅ モーダル操作
        addBtn.addEventListener('click', () => this.addModal.classList.remove('hidden'));
        cancelBtn.addEventListener('click', () => this.addModal.classList.add('hidden'));

        // ✅ 予定保存
        saveBtn.addEventListener('click', async () => {
          if (this.isSaving) return;
          this.isSaving = true;
          saveBtn.disabled = true;

          const type = document.getElementById('eventType').value;
          const item_name = document.getElementById('eventItem').value.trim();
          const quantity = document.getElementById('eventQuantity').value;
          const notes = document.getElementById('eventNotes').value.trim();
          const storeUrl = @json(route('calendar.store'));

          try {
            const res = await fetch(storeUrl, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              credentials: 'same-origin',
              body: JSON.stringify({ type, date: this.selectedDate, item_name, quantity, notes })
            });

            const data = await res.json();
            console.log('📨 レスポンス:', data);

            if (data.success) {
              this.addModal.classList.add('hidden');
              await this.fetchEvents(this.selectedDate);
              if (this.calendarInstance) {
                this.calendarInstance.refetchEvents();
              }
              alert('✅ 予定を追加しました');
            } else if (data.multiple) {
              alert('⚠️ 同名商品が複数あります');
            } else {
              alert('予定の追加に失敗しました');
            }
          } catch (err) {
            console.error('通信エラー:', err);
            alert('通信エラーが発生しました');
          } finally {
            this.isSaving = false;
            saveBtn.disabled = false;
          }
        });
      }
    };
  }
</script>


  
</x-app-layout>

<x-app-layout>
  <script src="https://unpkg.com/alpinejs" defer></script>

  <div x-data="{ time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }"
       x-init="setInterval(() => time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}), 1000)"
       class="min-h-screen flex flex-col bg-gradient-to-b from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">

      <!-- 🌟 上部バー -->
  <div class="flex justify-between sm:justify-end items-center w-full px-4 sm:px-10 py-3 sm:py-6 bg-white/80 backdrop-blur-md shadow-md">
    <div class="text-xl sm:text-4xl font-extrabold text-blue-600 tracking-widest drop-shadow" x-text="time"></div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
          class="bg-gray-800 text-white text-xs sm:text-lg px-3 sm:px-6 py-1 sm:py-3 rounded-full hover:bg-gray-900 active:scale-95 transition">
        ログアウト
      </button>
    </form>
  </div>

    <!-- 🧭 メイン -->
    <main class="flex-1 flex flex-col items-center py-6 sm:py-12 px-4 sm:px-6">
      <div class="w-full max-w-5xl">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white mb-8 sm:mb-10 text-center tracking-wide">
          メニュー
        </h2>



        <!-- 📦 グリッド -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">

          <!-- 📋 メニューカード共通スタイル -->
          @php
            $card = "bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col gap-2 sm:gap-3 p-5 sm:p-8 border border-transparent hover:border-indigo-200 active:scale-[0.98]";
            $title = "sm:text-2xl font-semibold text-gray-800 flex items-center gap-2";
            $desc  = "text-sm sm:text-base text-gray-500 leading-snug";
          @endphp

          <!-- モード通知 -->
          @if (session('mode') === 'home')
            <a href="{{ route('dashboard.home') }}" class="{{ $card }}">
              <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">🏠 家庭向け通知</h3>
              <p class="hidden sm:block {{ $desc }}">家庭モード用のお知らせを確認できます。</p>
            </a>
          @elseif (session('mode') === 'company')
            <a href="{{ route('dashboard.company') }}" class="{{ $card }}">
              <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">🏢 企業向け通知</h3>
              <p class="hidden sm:block {{ $desc }}">企業モード用のお知らせを確認できます。</p>
            </a>
          @else
            <a href="{{ route('mode.select') }}" class="{{ $card }}">
              <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">⚙️ モード未選択</h3>
              <p class="hidden sm:block {{ $desc }}">モードを選択してから通知を確認できます。</p>
            </a>
          @endif

          <!-- 在庫一覧 -->
          <a href="{{ route('items.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">📦 在庫一覧</h3>
            <p class="hidden sm:block {{ $desc }}">登録されている在庫を確認・編集できます。</p>
          </a>

          <!-- 在庫追加 -->
          <a href="{{ route('items.create') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">➕ 在庫追加</h3>
            <p class="hidden sm:block {{ $desc }}">在庫を新規登録できます。</p>
          </a>

          <!-- 入出庫カレンダー -->
          <a href="{{ route('calendar.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">📅 入出庫</h3>
            <p class="hidden sm:block {{ $desc }}">入庫・出庫の予定を管理します。</p>
          </a>

          <!-- 購入リスト -->
          <a href="{{ route('purchase_lists.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">🛒 購入リスト</h3>
            <p class="hidden sm:block {{ $desc }}">買う必要のあるアイテムを管理します。</p>
          </a>

          <!-- レシピ -->
          <a href="{{ route('recipes.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">🍳 レシピ</h3>
            <p class="hidden sm:block {{ $desc }}">作れる料理を提案します。</p>
          </a>

          <!-- ブックマーク -->
          <a href="{{ route('bookmarks.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">🔖 ブックマーク</h3>
            <p class="hidden sm:block {{ $desc }}">保存したレシピを一覧表示します。</p>
          </a>

          <!-- 監査ログ -->
          <a href="{{ route('audit-logs.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">📜 監査ログ</h3>
            <p class="hidden sm:block {{ $desc }}">操作履歴を確認します。</p>
          </a>

          <!-- CSV管理 -->
          <a href="{{ route('items.csv.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">📊 在庫CSV</h3>
            <p class="hidden sm:block {{ $desc }}">在庫データをCSVで管理します。</p>
          </a>

          <!-- 管理者設定 -->
          <a href="{{ route('admin.settings.dashboard') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-[0.95rem] sm:text-2xl truncate text-center">⚙️ 管理者設定</h3>
            <p class="hidden sm:block {{ $desc }}">管理者設定ページ（Dashboard）を開きます。</p>
          </a>
        </div>
      </div>
    </main>
  </div>
</x-app-layout>

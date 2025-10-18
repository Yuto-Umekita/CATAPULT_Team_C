<x-app-layout>
  <script src="https://unpkg.com/alpinejs" defer></script>

  <div x-data="{ time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }"
       x-init="setInterval(() => time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}), 1000)"
       class="min-h-screen flex flex-col bg-gradient-to-br from-blue-50 to-indigo-100">

    <!-- 🌟 上部バー（右寄せ：時間＋ログアウト） -->
    <div class="flex justify-end items-center w-full px-10 py-6 bg-white shadow-md gap-8">
      <!-- 現在時刻 -->
      <div class="text-4xl font-bold text-blue-600 tracking-widest" x-text="time"></div>

      <!-- ログアウトボタン -->
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="bg-gray-800 text-white px-6 py-3 rounded-xl text-lg hover:bg-gray-900 transition">
          ログアウト
        </button>
      </form>
    </div>

    <!-- 🧭 メインコンテンツ -->
    <main class="flex-1 flex flex-col items-center py-12 px-6">
      <div class="w-full max-w-6xl">
        <h2 class="text-3xl font-bold text-gray-800 mb-10 text-center">メニュー</h2>

        <!-- 📦 メニューグリッド -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">


          <!-- 🔔 通知 (モードによる切り替え) -->
          @if (session('mode') === 'home')
            <a href="{{ route('dashboard.home') }}" 
               class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl border border-yellow-200 hover:border-yellow-400 transition-all duration-300 flex flex-col gap-3">
              <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">🏠 家庭向け通知</h3>
              <p class="text-base text-gray-500">家庭モード用のお知らせを確認できます。</p>
            </a>
          @elseif (session('mode') === 'company')
            <a href="{{ route('dashboard.company') }}" 
               class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl border border-yellow-200 hover:border-yellow-400 transition-all duration-300 flex flex-col gap-3">
              <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">🏢 企業向け通知</h3>
              <p class="text-base text-gray-500">企業モード用のお知らせを確認できます。</p>
            </a>
          @else
            <a href="{{ route('mode.select') }}" 
               class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl border border-gray-300 hover:border-gray-400 transition-all duration-300 flex flex-col gap-3">
              <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">⚙️ モード未選択</h3>
              <p class="text-base text-gray-500">モードを選択してから通知を確認できます。</p>
            </a>
          @endif

          <!-- 在庫一覧 -->
          <a href="{{ route('items.index') }}" 
             class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl transition-all duration-300 flex flex-col gap-3">
            <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">📦 在庫一覧</h3>
            <p class="text-base text-gray-500">登録されている在庫を確認・編集できます。</p>
          </a>

          <!-- 在庫追加 -->
          <a href="{{ route('items.create') }}" 
             class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl transition-all duration-300 flex flex-col gap-3">
            <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">➕ 在庫追加</h3>
            <p class="text-base text-gray-500">在庫を新規登録できます。</p>
          </a>

          <!-- 購入リスト -->
          <a href="{{ route('purchase_lists.index') }}" 
             class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl transition-all duration-300 flex flex-col gap-3">
            <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">🛒 購入リスト</h3>
            <p class="text-base text-gray-500">買う必要のあるアイテムを管理します。</p>
          </a>

          <!-- 作れるレシピ -->
          <a href="{{ route('recipes.index') }}" 
             class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl transition-all duration-300 flex flex-col gap-3">
            <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">🍳 作れるレシピ</h3>
            <p class="text-base text-gray-500">在庫から作れる料理を提案します。</p>
          </a>

          <!-- ブックマーク -->
          <a href="{{ route('bookmarks.index') }}" 
             class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl transition-all duration-300 flex flex-col gap-3">
            <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">🔖 ブックマーク</h3>
            <p class="text-base text-gray-500">保存したレシピを一覧表示します。</p>
          </a>

          <!-- 監査ログ -->
          <a href="{{ route('audit-logs.index') }}" 
             class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl transition-all duration-300 flex flex-col gap-3">
            <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">📜 監査ログ</h3>
            <p class="text-base text-gray-500">操作履歴を確認します。</p>
          </a>

          <!-- 📊 在庫CSV管理 -->
          <a href="{{ route('items.csv.index') }}" 
             class="bg-white rounded-3xl shadow-md p-8 hover:shadow-2xl transition-all duration-300 flex flex-col gap-3 border border-blue-200 hover:border-blue-400">
            <h3 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">📊 在庫CSV管理</h3>
            <p class="text-base text-gray-500">在庫データをCSVでインポート・エクスポートできます。</p>
          </a>


      </div>
    </main>
  </div>
</x-app-layout>

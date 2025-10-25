<x-app-layout>
  <script src="https://unpkg.com/alpinejs" defer></script>

  @php
      // 🧭 セッションからモードとグループ情報を取得
      $currentMode = session('mode');
      $selectedGroupId = session('selected_group_id');
      $selectedGroup = $selectedGroupId ? \App\Models\Group::find($selectedGroupId) : null;

      // 表示用モードラベル
      $modeLabel = match ($currentMode) {
          'household' => '🏠 家庭用',
          'company'   => '🏢 企業用',
          default     => '⚙️ 未選択',
      };
  @endphp

  <div 
    x-data="{ time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }"
    x-init="setInterval(() => time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}), 1000)"
    class="min-h-screen flex flex-col bg-gradient-to-b from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800"
  >
    {{-- 🌟 上部バー（中央にタイトル、右側にグループとログアウト） --}}
<div class="relative flex justify-end items-center w-full px-4 sm:px-10 py-3 sm:py-5 bg-white/80 backdrop-blur-md shadow-md space-x-4 sm:space-x-6">

  {{-- 🎯 中央タイトル「メニュー」 --}}
  <h2 class="absolute left-1/2 transform -translate-x-1/2 text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white tracking-wide">
    メニュー
  </h2>

  {{-- 🏷 現在のグループ --}}
  @php
      $currentGroup = session('selected_group_id')
          ? \App\Models\Group::find(session('selected_group_id'))
          : null;
  @endphp
  @if($currentGroup)
      <div class="flex items-center gap-2 bg-gradient-to-r from-blue-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 px-3 py-1 rounded-full border border-blue-100 dark:border-gray-600 text-sm sm:text-base">
          <span class="text-gray-700 dark:text-gray-200 font-medium">
              🏷 {{ $currentGroup->name }}
              <span class="text-gray-500 dark:text-gray-400">
                  （{{ $currentGroup->mode === 'household' ? '家庭用' : '企業用' }}）
              </span>
          </span>
          <a href="{{ route('group.select') }}"
              class="text-blue-600 dark:text-blue-400 hover:underline text-xs sm:text-sm">
              切り替え
          </a>
      </div>
  @endif

</div>



    {{-- 🧭 メイン --}}
    <main class="flex-1 flex flex-col items-center py-6 sm:py-12 px-4 sm:px-6">
      <div class="w-full max-w-5xl">

        {{-- 📦 メニューグリッド --}}
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          @php
            $card = "bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col gap-2 sm:gap-3 p-5 sm:p-8 border border-transparent hover:border-indigo-200 active:scale-[0.98]";
            $title = "sm:text-2xl font-semibold text-gray-800 flex items-center gap-2";
            $desc  = "text-sm sm:text-base text-gray-500 leading-snug";
          @endphp

          {{-- モード通知 --}}
          @if ($currentMode === 'household')
            <a href="{{ route('home.dashboard') }}" class="{{ $card }}">
              <h3 class="{{ $title }} text-center">🏠 家庭向け通知</h3>
              <p class="hidden sm:block {{ $desc }}">家庭モード用のお知らせを確認できます。</p>
              </a>

          @elseif ($currentMode === 'company')
            <a href="{{ route('company.dashboard') }}" class="{{ $card }}">
              <h3 class="{{ $title }} text-center">🏢 企業向け通知</h3>
                <p class="hidden sm:block {{ $desc }}">企業モード用のお知らせを確認できます。</p>
            </a>

          @else
            <a href="{{ route('mode.select') }}" class="{{ $card }}">
              <h3 class="{{ $title }} text-center">⚙️ モード未選択</h3>
              <p class="hidden sm:block {{ $desc }}">モードを選択してから通知を確認できます。</p>
            </a>
          @endif


          {{-- 在庫一覧 --}}
          <a href="{{ route('items.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-center">📦 在庫一覧</h3>
            <p class="hidden sm:block {{ $desc }}">登録されている在庫を確認・編集できます。</p>
          </a>

          {{-- 在庫追加 --}}
          <a href="{{ route('items.create') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-center">➕ 在庫追加</h3>
            <p class="hidden sm:block {{ $desc }}">在庫を新規登録できます。</p>
          </a>

          {{-- 入出庫カレンダー --}}
          <a href="{{ route('calendar.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-center">📅 入出庫</h3>
            <p class="hidden sm:block {{ $desc }}">入庫・出庫の予定を管理します。</p>
          </a>

          {{-- 購入リスト --}}
          <a href="{{ route('purchase_lists.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-center">🛒 購入リスト</h3>
            <p class="hidden sm:block {{ $desc }}">買う必要のあるアイテムを管理します。</p>
          </a>

          {{-- レシピ --}}
          <a href="{{ route('recipes.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-center">🍳 レシピ</h3>
            <p class="hidden sm:block {{ $desc }}">作れる料理を提案します。</p>
          </a>

          {{-- ブックマーク --}}
          <a href="{{ route('bookmarks.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-center">🔖 ブックマーク</h3>
            <p class="hidden sm:block {{ $desc }}">保存したレシピを一覧表示します。</p>
          </a>

          {{-- 監査ログ --}}
          <a href="{{ route('audit-logs.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-center">📜 監査ログ</h3>
            <p class="hidden sm:block {{ $desc }}">操作履歴を確認します。</p>
          </a>

          {{-- CSV管理 --}}
          <a href="{{ route('items.csv.index') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-center">📊 在庫CSV</h3>
            <p class="hidden sm:block {{ $desc }}">在庫データをCSVで管理します。</p>
          </a>

          {{-- 管理者設定 --}}
          <a href="{{ route('admin.password.gate.show') }}" class="{{ $card }}">
            <h3 class="{{ $title }} text-center">⚙️ 管理者設定</h3>
            <p class="hidden sm:block {{ $desc }}">パスワード入力後に管理者設定ページを開きます。</p>
          </a>
        </div>
      </div>
    </main>
  </div>
</x-app-layout>

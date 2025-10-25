<x-guest-layout>
  <script src="https://unpkg.com/alpinejs" defer></script>

  {{-- 🔵 背景 --}}
  <div class="fixed inset-0 bg-gradient-to-b from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800"></div>

  {{-- 🔵 メイン --}}
  <div class="relative z-10 min-h-screen flex flex-col items-center justify-center">
      <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg w-full max-w-md text-center">

          {{-- タイトル --}}
          <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">
              管理者パスワードを入力してください
          </h2>

          {{-- 🌟 初回パスワード表示（手動で閉じるタイプ） --}}
          @if (session('first_time_info'))
              <div 
                  x-data="{ show: true }" 
                  x-show="show" 
                  x-transition.duration.300ms
                  class="relative bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 mb-6 rounded-xl shadow-sm text-left"
              >
                  <button 
                      @click="show = false"
                      class="absolute top-2 right-3 text-yellow-700 hover:text-yellow-900 text-xl font-bold leading-none focus:outline-none"
                      title="閉じる"
                  >
                      ×
                  </button>
                  <p class="font-semibold">⚠️ {{ session('first_time_info') }}</p>
              </div>
          @endif

          {{-- ❌ エラー表示 --}}
          @if ($errors->any())
              <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-xl text-left">
                  {{ $errors->first('password') }}
              </div>
          @endif

          {{-- 入力フォーム --}}
          <form method="POST" action="{{ route('admin.gate.check') }}">
              @csrf
              <div class="mb-6 text-left">
                  <label for="password" class="block text-gray-700 dark:text-gray-200 font-medium mb-2">
                      共通管理者パスワード
                  </label>
                  <input type="password" name="password" id="password" required
                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-400 focus:border-indigo-400">
              </div>

              {{-- ✅ ボタン右寄せ --}}
              <div class="flex justify-end">
                  <x-primary-button class="w-auto px-4 py-2 text-sm sm:text-base">
                      確認
                  </x-primary-button>
              </div>
          </form>

      </div>
  </div>
</x-guest-layout>

<x-guest-layout>
  {{-- 💡 Jetstream デフォルト背景を打ち消す --}}
  <style>
    /* デフォルトの灰色背景をリセット */
    body > div.min-h-screen {
      background: none !important;
    }
  </style>

  {{-- 🌈 全画面グラデーション背景 --}}
  <div class="min-h-screen w-full flex flex-col justify-center items-center 
              bg-gradient-to-br from-indigo-100 via-white to-pink-100 
              dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 
              bg-cover bg-center transition-all duration-700">

    {{-- 🏷️ タイトル --}}
    <div class="text-center mb-8">
      <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
        ようこそ！
      </h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">
        アカウントにログインして始めましょう
      </p>
    </div>

    {{-- 💡 ログインフォームカード --}}
    <div class="w-[90%] sm:w-full max-w-md bg-white dark:bg-gray-800 
                shadow-2xl rounded-2xl p-8 border border-gray-100 dark:border-gray-700 
                transition-all duration-300 hover:scale-[1.01]">

      <x-auth-session-status class="mb-4" :status="session('status')" />

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- メール -->
        <div>
          <x-input-label for="email" :value="__('メールアドレス')" class="text-gray-700 dark:text-gray-300" />
          <x-text-input id="email"
                        class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 
                               dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-400" 
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- パスワード -->
        <div class="mt-5">
          <x-input-label for="password" :value="__('パスワード')" class="text-gray-700 dark:text-gray-300" />
          <x-text-input id="password"
                        class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 
                               dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-400"
                        type="password" name="password" required autocomplete="current-password" />
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- ボタン -->
        <div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-3">
          @if (Route::has('password.request'))
            <a class="text-sm text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 transition underline"
               href="{{ route('password.request') }}">
              {{ __('パスワードをお忘れですか？') }}
            </a>
          @endif

          <x-primary-button 
              class="w-full sm:w-auto bg-indigo-500 hover:bg-indigo-600 
                     dark:bg-indigo-600 dark:hover:bg-indigo-500 
                     transition-all duration-300 transform hover:scale-[1.03]">
            {{ __('ログイン') }}
          </x-primary-button>
        </div>

        <!-- 新規登録リンク -->
        <div class="text-center mt-6">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            アカウントをお持ちでないですか？
            <a href="{{ route('register') }}" 
               class="text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold underline">
              新規登録はこちら
            </a>
          </p>
        </div>
      </form>
    </div>

    {{-- フッター --}}
    <footer class="mt-10 text-gray-400 text-xs text-center">
      © {{ date('Y') }} もちログ.
    </footer>
  </div>
</x-guest-layout>

<x-guest-layout>
  {{-- 💡 Jetstream/Breeze のデフォルト背景を無効化 --}}
  <style>
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
        新規登録
      </h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">
        アカウントを作成してサービスを始めましょう
      </p>
    </div>

    {{-- 📋 登録フォームカード --}}
    <div class="w-[90%] sm:w-full max-w-md bg-white dark:bg-gray-800 
                shadow-2xl rounded-2xl p-8 border border-gray-100 dark:border-gray-700 
                transition-all duration-300 hover:scale-[1.01]">

      {{-- 🔐 登録フォーム --}}
      <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- 👤 登録者名 -->
        <div>
          <x-input-label for="name" :value="__('登録者名')" class="text-gray-700 dark:text-gray-300" />
          <x-text-input id="name" 
                        class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 
                               dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-400"
                        type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
          <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- 📧 メールアドレス -->
        <div class="mt-5">
          <x-input-label for="email" :value="__('メールアドレス')" class="text-gray-700 dark:text-gray-300" />
          <x-text-input id="email"
                        class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 
                               dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-400"
                        type="email" name="email" :value="old('email')" required autocomplete="username" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- 🔒 パスワード -->
        <div class="mt-5">
          <x-input-label for="password" :value="__('パスワード')" class="text-gray-700 dark:text-gray-300" />
          <x-text-input id="password"
                        class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 
                               dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-400"
                        type="password" name="password" required autocomplete="new-password" />
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- ✅ パスワード確認 -->
        <div class="mt-5">
          <x-input-label for="password_confirmation" :value="__('パスワードを認証する')" class="text-gray-700 dark:text-gray-300" />
          <x-text-input id="password_confirmation"
                        class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 
                               dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-400"
                        type="password" name="password_confirmation" required autocomplete="new-password" />
          <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- 🚀 ボタンエリア -->
        <div class="flex flex-col sm:flex-row items-center justify-between mt-8 gap-3">

          {{-- 🔗 ログインページへのリンク --}}
          <a class="text-sm text-indigo-500 hover:text-indigo-600 dark:text-indigo-400 dark:hover:text-indigo-300 transition underline w-full sm:w-auto text-center sm:text-left"
             href="{{ route('login') }}">
            {{ __('すでにアカウントをお持ちの方はこちら') }}
          </a>

          <!-- 🚀 ボタン行 -->
        <div class="mt-8 flex justify-end">
        <div class="mt-8 flex justify-end sm:justify-end">
            <x-primary-button 
                class="w-full sm:w-auto justify-center text-center !flex !items-center !justify-center">
                {{ __('登録') }}
            </x-primary-button>
            </div>

        </div>



        </div>
      </form>
    </div>

    {{-- ⚙️ フッター --}}
    <footer class="mt-10 text-gray-400 text-xs text-center">
      © {{ date('Y') }} YourAppName. All rights reserved.
    </footer>
  </div>
</x-guest-layout>

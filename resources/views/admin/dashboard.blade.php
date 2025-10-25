<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-white leading-tight">
       管理者設定ページ
    </h2>
  </x-slot>

  <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

      {{-- ✅ 成功・エラーメッセージ --}}
      @if (session('success'))
          <div class="bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 px-4 py-3 rounded-xl shadow mb-6 text-center text-sm sm:text-base">
              {{ session('success') }}
          </div>
      @endif
      @if (session('error'))
          <div class="bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 px-4 py-3 rounded-xl shadow mb-6 text-center text-sm sm:text-base">
              {{ session('error') }}
          </div>
      @endif

      {{-- ============================================= --}}
      {{-- 👥 ユーザー一覧（上部に移動） --}}
      {{-- ============================================= --}}
      <h3 class="text-lg sm:text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100">ユーザー</h3>

      {{-- 👤 現在のログインユーザー情報 --}}
      <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-md mb-6">
          <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base">
              現在ログイン中：
              <span class="font-semibold text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</span> さん（
              @if(auth()->user()->is_admin)
                  <span class="text-indigo-600 dark:text-indigo-300 font-semibold">管理者</span>
              @else
                  <span class="text-gray-500 dark:text-gray-400 font-semibold">一般ユーザー</span>
              @endif
              ）
          </p>
      </div>

      {{-- 📱 ユーザー一覧テーブル --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-x-auto border border-gray-100 dark:border-gray-700 mb-10">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm sm:text-base">
              <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                      <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">名前</th>
                      <th class="px-4 sm:px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">メール</th>
                      <th class="px-4 sm:px-6 py-3 text-center font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">権限</th>
                      <th class="px-4 sm:px-6 py-3"></th>
                  </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  @foreach ($users as $user)
                      <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                          <td class="px-4 sm:px-6 py-3 text-gray-800 dark:text-gray-100 break-words">{{ $user->name }}</td>
                          <td class="px-4 sm:px-6 py-3 text-gray-600 dark:text-gray-300 break-words">{{ $user->email }}</td>
                          <td class="px-4 sm:px-6 py-3 text-center">
                              @if ($user->is_admin)
                                  <span class="inline-block px-3 py-1 text-xs sm:text-sm font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-100 rounded-full">管理者</span>
                              @else
                                  <span class="inline-block px-3 py-1 text-xs sm:text-sm font-semibold bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-100 rounded-full">一般</span>
                              @endif
                          </td>
                          <td class="px-4 sm:px-6 py-3 text-right">
                              <form method="POST" action="{{ route('admin.toggle.self') }}">
                                  @csrf
                                  @if (auth()->user()->is_admin)
                                      {{-- 🔴 一般ユーザーに戻す --}}
                                      <button type="submit"
                                          class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                          一般ユーザーに戻す
                                      </button>
                                  @else
                                      {{-- 🔵 管理者に設定（青） --}}
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                        管理者に設定
                                    </button>

                                  @endif
                              </form>
                          </td>
                      </tr>
                  @endforeach
              </tbody>
          </table>
      </div>

      {{-- ============================================= --}}
      {{-- 🔐 共通管理者パスワードの更新（下部に移動） --}}
      {{-- ============================================= --}}
      <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-lg">
          <h3 class="text-lg sm:text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100">
              共通管理者パスワードの更新
          </h3>

          <form method="POST" action="{{ route('admin.password.update') }}" class="mt-6 space-y-6">
              @csrf
              @method('PUT')

              <div>
                  <x-input-label for="admin_password" :value="__('新しい共通パスワード')" />
                  <x-text-input id="admin_password" name="admin_password" type="password" class="mt-1 block w-full" />
                  <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
              </div>

              <div>
                  <x-input-label for="admin_password_confirmation" :value="__('新しい共通パスワード(確認用)')" />
                  <x-text-input id="admin_password_confirmation" name="admin_password_confirmation" type="password" class="mt-1 block w-full" />
                  <x-input-error :messages="$errors->get('admin_password_confirmation')" class="mt-2" />
              </div>

              <div class="flex items-center justify-end gap-4">
                  <x-primary-button>{{ __('パスワードを更新') }}</x-primary-button>
              </div>
          </form>
      </div>

  </div>
</x-app-layout>

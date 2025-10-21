<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-center text-gray-800 dark:text-gray-100 leading-tight">
      👤 プロフィール
    </h2>
  </x-slot>

  <div class="py-10 max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl overflow-hidden">

      {{-- 🟣 上部ヘッダー --}}
      <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-8 text-center text-white">
        <h3 class="text-2xl font-bold mb-2">{{ $user->name }}</h3>
        <p class="text-sm text-white/90">{{ $user->email }}</p>
        <p class="text-xs mt-2 opacity-80">
          登録日：{{ $user->created_at->format('Y年m月d日') }}
        </p>
      </div>

      {{-- 📋 詳細情報 --}}
      <div class="p-8 space-y-6">
        {{-- 利用種別 --}}
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
          <h4 class="text-gray-600 dark:text-gray-300 text-sm font-semibold mb-1">利用種別</h4>
          <p class="text-lg text-gray-900 dark:text-gray-100">
            {{ $profile->user_type === 'enterprise' ? '企業用' : '家庭用' }}
          </p>
        </div>

        {{-- 家庭用 --}}
        @if ($profile->user_type === 'household')
          <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h4 class="text-gray-600 dark:text-gray-300 text-sm font-semibold mb-1">性別</h4>
            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $profile->gender ?? '未設定' }}</p>
          </div>

          <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h4 class="text-gray-600 dark:text-gray-300 text-sm font-semibold mb-1">年齢</h4>
            <p class="text-lg text-gray-900 dark:text-gray-100">
              {{ $profile->age ? $profile->age . '歳' : '未設定' }}
            </p>
          </div>

          <div class="pb-4">
            <h4 class="text-gray-600 dark:text-gray-300 text-sm font-semibold mb-1">職業</h4>
            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $profile->occupation ?? '未設定' }}</p>
          </div>
        @endif

        {{-- 企業用 --}}
        @if ($profile->user_type === 'enterprise')
          <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h4 class="text-gray-600 dark:text-gray-300 text-sm font-semibold mb-1">会社名</h4>
            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $profile->company_name ?? '未設定' }}</p>
          </div>

          <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h4 class="text-gray-600 dark:text-gray-300 text-sm font-semibold mb-1">役職</h4>
            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $profile->position ?? '未設定' }}</p>
          </div>

          <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h4 class="text-gray-600 dark:text-gray-300 text-sm font-semibold mb-1">電話番号</h4>
            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $profile->phone ?? '未設定' }}</p>
          </div>

          <div class="pb-4">
            <h4 class="text-gray-600 dark:text-gray-300 text-sm font-semibold mb-1">連絡用メール</h4>
            <p class="text-lg text-gray-900 dark:text-gray-100">{{ $profile->contact_email ?? '未設定' }}</p>
          </div>
        @endif
      </div>

      {{-- ✏️ 編集ボタン --}}
      <div class="p-6 text-center bg-gray-50 dark:bg-gray-900/50 border-t dark:border-gray-700">
        <a href="{{ route('profile.edit') }}"
           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition">
          ✏️ プロフィールを編集
        </a>
      </div>
    </div>
  </div>
</x-app-layout>

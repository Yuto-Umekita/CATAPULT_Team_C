<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
          メンバーを追加
      </h2>
  </x-slot>

  <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-4">{{ $group->name }}</h3>

      <form method="POST" action="{{ route('group.members.store', $group) }}">
          @csrf

          <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">招待するユーザーのメールアドレス</label>
              <input type="email" name="email" class="w-full border rounded-md p-2" placeholder="user@example.com" required>
          </div>

          <div class="flex justify-end">
              <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                  追加
              </button>
          </div>
      </form>

      <a href="{{ route('group.members.index', $group) }}" class="block text-center text-gray-600 hover:underline mt-4">
          ← 戻る
      </a>
  </div>
</x-app-layout>

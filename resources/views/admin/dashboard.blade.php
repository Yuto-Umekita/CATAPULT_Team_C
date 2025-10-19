<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-3xl text-gray-800 dark:text-gray-100 leading-tight text-center">
       管理者設定ページ
    </h2>
  </x-slot>

  <div class="max-w-5xl mx-auto py-10 px-6">
      @if (session('success'))
          <div class="bg-green-100 text-green-800 px-4 py-3 rounded-xl shadow mb-6 text-center">
              {{ session('success') }}
          </div>
      @endif
      @if (session('error'))
          <div class="bg-red-100 text-red-800 px-4 py-3 rounded-xl shadow mb-6 text-center">
              {{ session('error') }}
          </div>
      @endif

      <h3 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100">ユーザー一覧</h3>

      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-700">
                  <tr>
                      <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">名前</th>
                      <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">メール</th>
                      <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-200">権限</th>
                      <th class="px-6 py-3"></th>
                  </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  @foreach (\App\Models\User::all() as $user)
                      <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                          <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                          <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                          <td class="px-6 py-4 text-center">
                              @if ($user->is_admin)
                                  <span class="px-3 py-1 text-xs font-semibold bg-indigo-100 text-indigo-700 rounded-full">管理者</span>
                              @else
                                  <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full">一般</span>
                              @endif
                          </td>
                          <td class="px-6 py-4 text-right">
                              <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST">
                                  @csrf
                                  <button type="submit"
                                      class="px-4 py-2 rounded-lg text-white text-sm font-medium 
                                             {{ $user->is_admin ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600' }}">
                                      {{ $user->is_admin ? '一般ユーザーに設定' : '管理者に設定' }}
                                  </button>
                              </form>
                          </td>
                      </tr>
                  @endforeach
              </tbody>
          </table>
      </div>
  </div>
</x-app-layout>

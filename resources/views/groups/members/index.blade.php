<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
          グループメンバー一覧
      </h2>
  </x-slot>

  <div class="max-w-3xl mx-auto mt-8 bg-white rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold mb-4">{{ $group->name }}</h3>

      <a href="{{ route('group.members.create', $group) }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
          ➕ メンバーを追加
      </a>

      <table class="w-full mt-6 border">
          <thead>
              <tr class="bg-gray-100">
                  <th class="p-2 text-left">名前</th>
                  <th class="p-2 text-left">メールアドレス</th>
                  <th class="p-2 text-left">役割</th>
                  <th class="p-2 text-center">操作</th>
              </tr>
          </thead>
          <tbody>
              @foreach($members as $member)
              <tr class="border-t">
                  <td class="p-2">{{ $member->name }}</td>
                  <td class="p-2">{{ $member->email }}</td>
                  <td class="p-2">{{ $member->pivot->role }}</td>
                  <td class="p-2 text-center">
                      @if($member->id !== auth()->id())
                      <form action="{{ route('group.members.destroy', [$group, $member]) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="text-red-500 hover:underline"
                              onclick="return confirm('本当に削除しますか？')">削除</button>
                      </form>
                      @else
                      <span class="text-gray-400 text-sm">（自分）</span>
                      @endif
                  </td>
              </tr>
              @endforeach
          </tbody>
      </table>
  </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight text-center">
            グループ一覧
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-10 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
        <a href="{{ route('groups.create') }}" 
           class="inline-block mb-6 bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
           ➕ 新規グループ作成
        </a>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <th class="py-2 px-4 text-left">グループ名</th>
                    <th class="py-2 px-4 text-left">モード</th>
                    <th class="py-2 px-4 text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                    <tr class="border-b dark:border-gray-700">
                        <td class="py-2 px-4">{{ $group->name }}</td>
                        <td class="py-2 px-4">{{ $group->mode === 'household' ? '家庭用' : '企業用' }}</td>
                        <td class="py-2 px-4 text-center space-x-2">
                            <a href="{{ route('groups.edit', $group) }}" 
                               class="text-blue-500 hover:underline">編集</a>

                            <form action="{{ route('groups.destroy', $group) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('本当に削除しますか？');"
                                        class="text-red-500 hover:underline">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight text-center">
            グループを編集
        </h2>
    </x-slot>

    <div class="max-w-md mx-auto mt-10 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
        <form method="POST" action="{{ route('groups.update', $group) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 mb-1">グループ名</label>
                <input type="text" name="name" value="{{ old('name', $group->name) }}"
                    class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 mb-1">モード</label>
                <select name="mode"
                    class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="household" @selected($group->mode === 'household')>家庭用</option>
                    <option value="company" @selected($group->mode === 'company')>企業用</option>
                </select>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('groups.index') }}"
                   class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg">
                   戻る
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    保存
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

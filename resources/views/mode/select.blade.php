<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-white leading-tight text-center">
            {{ __('ログイン方法の選択') }}
        </h2>
    </x-slot>

    <div class="py-12 flex flex-col items-center justify-center space-y-6">
        <p class="text-gray-700 dark:text-gray-200 text-lg mb-4">
            ログイン方法を選択してください：
        </p>

        <form method="POST" action="{{ route('mode.store') }}">
            @csrf

            <div class="flex space-x-6">
                <button
                    type="submit"
                    name="user_type"
                    value="home"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg shadow">
                    家庭用
                </button>

                <button
                    type="submit"
                    name="user_type"
                    value="company"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg shadow">
                    企業用
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

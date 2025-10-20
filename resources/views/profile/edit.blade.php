<!-- resources/views/profile/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('プロフィール編集') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-green-600">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" 
      x-data="{ type: '{{ $currentMode ?? $profile->user_type }}' }">

                    @csrf
                    @method('PATCH')

                    {{-- 利用種別：家庭用 or 企業用 --}}
<div class="mb-6">
    <x-input-label for="user_type" :value="__('利用種別')" />

    {{-- モード固定用セレクト --}}
    <select id="user_type" name="user_type"
            x-model="type"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            @if(isset($currentMode)) disabled @endif>
        <option value="household" 
            @selected(($currentMode ?? old('user_type', $profile->user_type)) === 'household')>
            家庭用
        </option>
        <option value="enterprise" 
            @selected(($currentMode ?? old('user_type', $profile->user_type)) === 'enterprise')>
            企業用
        </option>
    </select>

    {{-- モード固定時も値を送信するための hidden --}}
    @if(isset($currentMode))
        <input type="hidden" name="user_type" value="{{ $currentMode }}">
    @endif

    <x-input-error class="mt-2" :messages="$errors->get('user_type')" />
</div>


                    {{-- 家庭用フォーム --}}
                    <div x-show="type === 'household'" x-cloak>
                        <div class="mb-4">
                            <x-input-label for="gender" :value="__('性別')" />
                            <select id="gender" name="gender" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">選択してください</option>
                                <option value="男性" @selected(old('gender', $profile->gender) === '男性')>男性</option>
                                <option value="女性" @selected(old('gender', $profile->gender) === '女性')>女性</option>
                                <option value="その他" @selected(old('gender', $profile->gender) === 'その他')>その他</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="age" :value="__('年齢')" />
                            <x-text-input id="age" name="age" type="number"
                                class="mt-1 block w-full"
                                :value="old('age', $profile->age)"
                                min="0" max="150" />
                            <x-input-error class="mt-2" :messages="$errors->get('age')" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="occupation" :value="__('職業')" />
                            <x-text-input id="occupation" name="occupation" type="text"
                                class="mt-1 block w-full"
                                :value="old('occupation', $profile->occupation)" />
                            <x-input-error class="mt-2" :messages="$errors->get('occupation')" />
                        </div>
                    </div>

                    {{-- 企業用フォーム --}}
                    <div x-show="type === 'enterprise'" x-cloak>
                        <div class="mb-4">
                            <x-input-label for="contact_email" :value="__('メール')" />
                            <x-text-input id="contact_email" name="contact_email" type="email"
                                class="mt-1 block w-full"
                                :value="old('contact_email', $profile->contact_email)" />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="phone" :value="__('電話')" />
                            <x-text-input id="phone" name="phone" type="text"
                                class="mt-1 block w-full"
                                :value="old('phone', $profile->phone)" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="company_name" :value="__('会社名')" />
                            <x-text-input id="company_name" name="company_name" type="text"
                                class="mt-1 block w-full"
                                :value="old('company_name', $profile->company_name)" />
                            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="position" :value="__('役職')" />
                            <x-text-input id="position" name="position" type="text"
                                class="mt-1 block w-full"
                                :value="old('position', $profile->position)" />
                            <x-input-error class="mt-2" :messages="$errors->get('position')" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>保存する</x-primary-button>
                    </div>
                </form>

                {{-- Alpine 未導入の場合は、レイアウトに以下を追加:
                <script src="https://unpkg.com/alpinejs" defer></script>
                --}}
            </div>
        </div>
    </div>
</x-app-layout>

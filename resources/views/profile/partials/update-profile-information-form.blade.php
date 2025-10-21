<section>
    <header>
        <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('プロフィール') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("登録者名とメールアドレスの更新") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('登録者名')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
    <x-input-label for="gender" :value="_('性別')" />
    <select id="gender" name="gender" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">選択してください</option>
        <option value="男性" @if (old('gender', $user->gender) === '男性') selected @endif>男性</option>
        <option value="女性" @if (old('gender', $user->gender) === '女性') selected @endif>女性</option>
        <option value="その他" @if (old('gender', $user->gender) === 'その他') selected @endif>その他</option>
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('gender')" />
</div>

<div>
    <x-input-label for="occupation" :value="__('職業')" />
    <x-text-input id="occupation" name="occupation" type="text" class="mt-1 block w-full" :value="old('occupation', $user->occupation)" autocomplete="occupation" />
    <x-input-error class="mt-2" :messages="$errors->get('occupation')" />
</div>

        <div>
            <x-input-label for="email" :value="__('メールアドレス')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('メールアドレス認証がされていません') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('確認メールを再送信するにはここをクリックしてください') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('新しい確認リンクがあなたのメールアドレスに送信されました') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="('電話番号')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="company" :value="('会社名')" />
            <x-text-input id="company" name="company" type="text" class="mt-1 block w-full" :value="old('company', $user->company)" autocomplete="organization" />
            <x-input-error class="mt-2" :messages="$errors->get('company')" />
        </div>

        <div>
            <x-input-label for="position" :value="_('役職')" />
            <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $user->position)" autocomplete="organization-title" />
            <x-input-error class="mt-2" :messages="$errors->get('position')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('保存') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('保存しました') }}</p>
            @endif
        </div>
    </form>
</section>

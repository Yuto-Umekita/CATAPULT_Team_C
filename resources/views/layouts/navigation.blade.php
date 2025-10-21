<nav x-data="{ open: false }" class="bg-[#bae98dff] border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('通知') }}
                    </x-nav-link>

                    <x-nav-link :href="route('items.index')" :active="request()->routeIs('items.index')">
                        {{ __('在庫一覧') }}
                    </x-nav-link>

                    <x-nav-link :href="route('items.create')" :active="request()->routeIs('items.create')">
                        {{ __('追加') }}
                    </x-nav-link>

                    <x-nav-link :href="route('purchase_lists.index')" :active="request()->routeIs('purchase_lists.index')">
                        {{ __('購入予定品') }}
                    </x-nav-link>

                    <x-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.index')">
                        {{ __('履歴') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('admin.login')" :active="request()->routeIs('admin.login')">
                        {{ __('管理者ログイン') }}
                    </x-nav-link>
                   
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 border border-transparent text-xs sm:text-sm leading-4 font-medium rounded-md text-gray-500 focus:outline-none transition ease-in-out duration-150">
                            @auth
                                <img src="{{ Auth::user()->profile_photo_url ?? asset('images/default-icon.png') }}" 
                                    alt="Profile" 
                                    class="w-7 h-7 sm:w-9 sm:h-9 rounded-full mr-2 border border-gray-300">
                                <div class="hidden sm:block">{{ Auth::user()->name }}</div>
                            @endauth
                        </button>

                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.show')">
                            {{ __('プロフィール') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('ログアウト') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('通知') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('items.index')" :active="request()->routeIs('items.index')">
                {{ __('在庫一覧') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('items.create')" :active="request()->routeIs('items.create')">
                {{ __('追加') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('purchase_lists.index')" :active="request()->routeIs('purchase_lists.index')">
                {{ __('購入予定品') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.index')">
                {{ __('履歴') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('admin.login')" :active="request()->routeIs('admin.login')">
                {{ __('管理者ログイン') }}
            </x-responsive-nav-link>
        </div>


            <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-400">
            <div class="px-4">
                @auth
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                @endauth

                @guest
                    <div class="font-medium text-base text-gray-800">ゲスト</div>
                @endguest
            </div>
          

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('プロフィール') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('ログアウト') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

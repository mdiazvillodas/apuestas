<nav x-data="{ open: false }"
     class="relative bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img
                            src="{{ asset('images/logo.png') }}"
                            alt="Sport Bets"
                            class="h-8 w-auto"
                        />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                        Events
                    </x-nav-link>

                    <x-nav-link :href="route('leaderboard.index')" :active="request()->routeIs('leaderboard.*')">
                        Leaderboard
                    </x-nav-link>

                    <x-nav-link :href="route('bets.my')" :active="request()->routeIs('bets.my')">
                        My Bets
                    </x-nav-link>

                    {{-- Admin dropdown (DESKTOP) --}}
                    @if(Auth::user()->role === 'admin')
                        <div class="relative inline-flex items-center h-full">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center h-full px-1 pt-1 border-b-2 border-transparent
                                               text-sm font-medium leading-5
                                               text-gray-700 dark:text-gray-300
                                               hover:text-gray-900 dark:hover:text-gray-100
                                               focus:outline-none transition">
                                        <span>Admin</span>
                                        <svg class="ml-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                  clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.events.index')">
                                        Events
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.events.create')">
                                        Create Event
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.bets.index')">
                                        Bets Log
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.teams.index')">
                                        Teams
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.coin-grants.index')">
                                        Coin Grants
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent
                                   text-sm leading-4 font-medium rounded-md
                                   text-gray-500 dark:text-gray-400
                                   bg-white dark:bg-gray-800
                                   hover:text-gray-700 dark:hover:text-gray-300
                                   focus:outline-none transition">
                            {{ Auth::user()->name }}
                            <svg class="ml-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                      clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md
                               text-gray-400 hover:text-gray-500 hover:bg-gray-100
                               focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }"
                              class="inline-flex"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }"
                              class="hidden"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu -->
    <div x-show="open" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('events.index')">Events</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('bets.my')">My Bets</x-responsive-nav-link>
        </div>
    </div>
</nav>

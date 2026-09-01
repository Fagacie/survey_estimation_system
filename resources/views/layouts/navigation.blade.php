<nav x-data="{ open: false }" style="background: #ffffff; border-bottom: 1px solid rgba(226, 232, 240, 0.8); box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02);">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; text-decoration: none;">
                        <x-application-logo class="block h-12 w-auto" style="color: #0f172a;" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')" 
                                style="border-bottom-width: 2px; padding-top: 2px; font-weight: 500; font-size: 0.95rem; color: {{ request()->routeIs('projects.*') ? '#0f172a' : '#64748b' }}; border-color: {{ request()->routeIs('projects.*') ? '#3b82f6' : 'transparent' }};">
                        {{ __('Projects') }}
                    </x-nav-link>

                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" 
                                style="border-bottom-width: 2px; padding-top: 2px; font-weight: 500; font-size: 0.95rem; color: {{ request()->routeIs('clients.*') ? '#0f172a' : '#64748b' }}; border-color: {{ request()->routeIs('clients.*') ? '#3b82f6' : 'transparent' }};">
                        {{ __('Clients') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('settings.costs')" :active="request()->routeIs('settings.*')"
                                style="border-bottom-width: 2px; padding-top: 2px; font-weight: 500; font-size: 0.95rem; color: {{ request()->routeIs('settings.*') ? '#0f172a' : '#64748b' }}; border-color: {{ request()->routeIs('settings.*') ? '#3b82f6' : 'transparent' }};">
                        {{ __('Settings') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.85rem; font-weight: 500; color: #475569; background: #f8fafc; cursor: pointer; transition: all 0.2s; gap: 0.5rem;">
                            <!-- Generic User Avatar -->
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div style="margin-left: 0.25rem;">{{ Auth::user()->name }}</div>

                            <div>
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" style="color: #94a3b8;">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Dropdown styling adjustment via generic tailwind overrides if needed, but x-dropdown is standard -->
                        <div style="padding: 0.25rem 0;">
                            <x-dropdown-link :href="route('profile.edit')" style="font-size: 0.9rem; color: #334155;">
                                <i class="fa-regular fa-user me-2 text-muted"></i> {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();" style="font-size: 0.9rem; color: #ef4444;">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('projects.*')" style="color: {{ request()->routeIs('projects.*') ? '#3b82f6' : '#475569' }};">
                {{ __('Projects') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" style="color: {{ request()->routeIs('clients.*') ? '#3b82f6' : '#475569' }};">
                {{ __('Clients') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('settings.costs')" :active="request()->routeIs('settings.*')" style="color: {{ request()->routeIs('settings.*') ? '#3b82f6' : '#475569' }};">
                {{ __('Settings') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t" style="border-color: #e2e8f0;">
            <div class="px-4">
                <div class="font-medium text-base" style="color: #0f172a;">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm" style="color: #64748b;">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" style="color: #475569;">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" style="color: #ef4444;">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<nav
    x-data="{
        open: false,
        collapsed: false,
        init() {
            const stored = localStorage.getItem('sidebar-collapsed');
            if (stored === '1') {
                this.collapsed = true;
            }
            this.$watch('collapsed', (val) => {
                localStorage.setItem('sidebar-collapsed', val ? '1' : '0');
            });
        },
    }"
    class="relative flex-shrink-0 lg:w-80 text-emerald-100"
>
    <div class="lg:hidden flex items-center justify-between px-4 py-3">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-ember-dark via-ember to-gold shadow-sm ring-1 ring-emerald-200/60">
                <img src="{{ asset(config('branding.logo_path')) }}" alt="{{ config('branding.logo_alt', 'Logo') }}" class="h-8 w-8" loading="lazy">
            </span>
            <span class="text-lg font-semibold text-emerald-100">{{ config('branding.name', config('app.name')) }}</span>
        </a>
        <button @click="open = true" class="inline-flex items-center justify-center rounded-full bg-white/10 p-2 text-emerald-100 ring-1 ring-white/10 shadow-sm transition hover:-translate-y-0.5 hover:bg-white/20 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-300/60">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm lg:hidden"
        @click="open = false"
    ></div>

    <div
        x-bind:class="[
            open ? 'translate-x-0 opacity-100' : '-translate-x-full opacity-0 lg:translate-x-0 lg:opacity-100',
            collapsed ? 'lg:w-24' : 'lg:w-80'
        ]"
        class="fixed inset-y-0 left-0 z-40 h-full w-80 transform bg-black/90 shadow-2xl shadow-black/60 ring-1 ring-white/10 backdrop-blur-2xl transition-all duration-300 ease-out lg:relative lg:inset-auto lg:z-0 lg:h-screen lg:flex lg:flex-col lg:opacity-100 lg:shadow-xl"
    >
        <div class="flex items-center justify-between px-5 pt-6">
            <button
                type="button"
                @click="collapsed = !collapsed"
                class="flex items-center rounded-full px-2 py-1 transition hover:-translate-y-0.5 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-emerald-300/60"
                :title="collapsed ? '{{ __('Expand sidebar') }}' : '{{ __('Collapse sidebar') }}'">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-ember-dark via-ember to-gold shadow-sm ring-1 ring-emerald-200/60">
                    <img src="{{ asset(config('branding.logo_path')) }}" alt="{{ config('branding.logo_alt', 'Logo') }}" class="h-9 w-9" loading="lazy">
                </span>
                <span class="ms-3 text-lg font-semibold text-emerald-100" x-show="!collapsed" x-transition>{{ config('branding.name', config('app.name')) }}</span>
            </button>
            <button @click="open = false" class="lg:hidden inline-flex items-center justify-center rounded-full bg-white/10 p-2 text-emerald-100 ring-1 ring-emerald-400/50 shadow-sm transition hover:bg-white/20 hover:text-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                &times;
            </button>
        </div>

        <div class="mt-4 flex-1 overflow-y-auto px-4 pb-6">
            <div class="flex flex-col gap-2">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" x-bind:class="collapsed ? 'justify-center' : 'justify-start'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3 3.5 9v12h7v-7h3v7h7V9z"/></svg>
                    <span x-show="!collapsed" x-transition>{{ __('Dashboard') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" x-bind:class="collapsed ? 'justify-center' : 'justify-start'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7 8a7 7 0 0 1 14 0Z"/></svg>
                    <span x-show="!collapsed" x-transition>{{ __('Customers') }}</span>
                </x-nav-link>

                <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')" x-bind:class="collapsed ? 'justify-center' : 'justify-start'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16v4H4Zm4 6h12v4H8Zm-4 6h16v4H4Z"/></svg>
                    <span x-show="!collapsed" x-transition>{{ __('Projects') }}</span>
                </x-nav-link>

                @role('admin')
                    <div class="pt-2 text-xs font-semibold uppercase tracking-[0.25em] text-emerald-200/80" x-show="!collapsed" x-transition>{{ __('Admin') }}</div>
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" x-bind:class="collapsed ? 'justify-center' : 'justify-start'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5ZM4 20a8 8 0 0 1 16 0Z"/></svg>
                        <span x-show="!collapsed" x-transition>{{ __('Users') }}</span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')" x-bind:class="collapsed ? 'justify-center' : 'justify-start'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 3 7v7a9 9 0 0 0 18 0V7Zm0 2.18L18.74 8 12 11.82 5.26 8ZM5 9.74l6 3.55v6.1A7 7 0 0 1 5 14Z"/></svg>
                        <span x-show="!collapsed" x-transition>{{ __('Roles') }}</span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.permissions.index')" :active="request()->routeIs('admin.permissions.*')" x-bind:class="collapsed ? 'justify-center' : 'justify-start'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a5 5 0 0 1 5 5v2h1a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-7a3 3 0 0 1 3-3h1V7a5 5 0 0 1 5-5Zm3 7V7a3 3 0 0 0-6 0v2Z"/></svg>
                        <span x-show="!collapsed" x-transition>{{ __('Permissions') }}</span>
                    </x-nav-link>
                @endrole
            </div>
        </div>

    </div>

    <div class="fixed top-4 right-4 z-50">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-emerald-100 shadow-md shadow-black/30 ring-1 ring-white/10 transition hover:-translate-y-0.5 hover:bg-white/20 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-300/60">
                    {{ __('Account') }}
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.23l3.71-4a.75.75 0 1 1 1.08 1.04l-4.24 4.58a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="px-4 py-2">
                    <p class="text-sm font-semibold text-emerald-100">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-emerald-200/70">{{ Auth::user()->email }}</p>
                </div>
                <div class="border-t border-white/10"></div>
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</nav>

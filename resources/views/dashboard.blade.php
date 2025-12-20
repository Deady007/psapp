<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-200/80">{{ __('Overview') }}</p>
                <h2 class="text-3xl font-semibold leading-tight text-white font-display">{{ __('Dashboard') }}</h2>
                <p class="text-sm text-slate-300">{{ __('Pulse check on customers, projects, and team momentum.') }}</p>
            </div>
            <a href="{{ route('projects.index') }}" class="soft-cta">
                {{ __('New Project') }}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h5.5V3.75a.75.75 0 0 1 1.5 0v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="soft-card p-6 transition duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_35px_70px_rgba(15,23,42,0.6)] motion-safe:animate-reveal reveal-delay-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Customers') }}</p>
                            <p class="mt-2 text-3xl font-bold text-white">0</p>
                        </div>
                        <span class="soft-badge motion-safe:animate-float">{{ __('Pulse') }}</span>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">{{ __('Track relationships and stay ahead with clean, calming visuals.') }}</p>
                </div>
                <div class="soft-card p-6 transition duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_35px_70px_rgba(15,23,42,0.6)] motion-safe:animate-reveal reveal-delay-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Projects') }}</p>
                            <p class="mt-2 text-3xl font-bold text-white">0</p>
                        </div>
                        <span class="soft-badge">{{ __('Flow') }}</span>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">{{ __('Smooth gradients and subtle motion keep focus on progress.') }}</p>
                </div>
                <div class="soft-card p-6 transition duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_35px_70px_rgba(15,23,42,0.6)] motion-safe:animate-reveal reveal-delay-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Team') }}</p>
                            <p class="mt-2 text-3xl font-bold text-white">0</p>
                        </div>
                        <span class="soft-badge">{{ __('Calm') }}</span>
                    </div>
                    <p class="mt-3 text-sm text-slate-300">{{ __('Enjoy a modern, marble-morphism inspired workspace everywhere.') }}</p>
                </div>
            </div>

            <div class="soft-card p-8 motion-safe:animate-reveal reveal-delay-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Quick Actions') }}</p>
                        <h3 class="text-xl font-semibold text-white font-display">{{ __('Jump back into your flow') }}</h3>
                        <p class="text-sm text-slate-300">{{ __('Use the shortcuts below to keep momentum with smooth transitions.') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('customers.index') }}" class="soft-cta">{{ __('View Customers') }}</a>
                        <a href="{{ route('projects.index') }}" class="soft-cta">{{ __('View Projects') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

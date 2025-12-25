<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('branding.name', config('app.name', 'Laravel')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-emerald-100">
        <div class="app-shell">
            <div class="mx-auto flex min-h-screen max-w-7xl flex-col gap-12 px-6 py-10 lg:flex-row lg:items-center lg:gap-16 lg:px-12">
                <div class="flex w-full flex-col gap-10 lg:w-1/2 motion-safe:animate-rise">
                    <a href="{{ url('/') }}" class="flex items-center gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-emerald-400/20 shadow-[0_12px_30px_rgba(0,0,0,0.6)]">
                            <x-application-logo class="h-7 w-7 fill-current text-emerald-200" />
                        </span>
                        <div class="flex flex-col">
                            <span class="text-lg font-semibold tracking-tight text-emerald-100">{{ config('branding.name', config('app.name', 'Laravel')) }}</span>
                            <span class="text-xs font-semibold uppercase tracking-[0.32em] text-emerald-200/70">Project Command</span>
                        </div>
                    </a>

                    <div class="flex flex-col gap-6">
                        <h1 class="text-4xl font-display leading-tight text-emerald-100 sm:text-5xl">
                            Orchestrate every project with calm, focused momentum.
                        </h1>
                        <p class="text-lg text-emerald-200/80">
                            Keep deliverables, budgets, and handoffs aligned with one workspace built for cross-functional teams.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="soft-card p-5">
                            <div class="flex flex-col gap-3">
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-200/80">Signal</p>
                                <p class="text-lg font-semibold text-emerald-100">Live status tracking for every milestone.</p>
                                <p class="text-sm text-emerald-200/70">Stay ahead of risks with early-warning insights.</p>
                            </div>
                        </div>
                        <div class="soft-card p-5">
                            <div class="flex flex-col gap-3">
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-200/80">Flow</p>
                                <p class="text-lg font-semibold text-emerald-100">Automated reminders and clean handoffs.</p>
                                <p class="text-sm text-emerald-200/70">Launch faster with fewer follow-ups.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="soft-badge">Realtime alerts</span>
                        <span class="soft-badge">Client-ready reports</span>
                        <span class="soft-badge">Unlimited collaborators</span>
                    </div>
                </div>

                <div class="flex w-full justify-center lg:w-1/2">
                    <div class="w-full max-w-lg motion-safe:animate-reveal reveal-delay-2">
                        <div class="soft-panel p-8 lg:p-10">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('vendor/adminlte/dist/js/jarvis.js') }}" defer></script>
    </body>
</html>

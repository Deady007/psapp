<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('branding.name', config('app.name', 'Laravel')) }} - Kanban</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Manrope:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Share+Tech+Mono&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" data-theme="softui" data-density="comfortable" data-motion="1" data-particles-intensity="med">
        <div class="app-shell">
            <div class="mx-auto flex min-h-screen max-w-7xl flex-col gap-8 px-6 py-8">
                <header class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('dashboard') }}" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-400/10 ring-1 ring-emerald-400/30 shadow-[0_12px_30px_rgba(0,0,0,0.6)]">
                            <x-application-logo class="h-7 w-7 fill-current text-emerald-200" />
                        </a>
                        <div class="flex flex-col">
                            <span class="text-lg font-semibold tracking-tight text-emerald-100">{{ config('branding.name', config('app.name', 'Laravel')) }}</span>
                            <span class="text-xs font-semibold uppercase tracking-[0.32em] text-emerald-300/70">Kanban</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-emerald-200/80">
                        @auth
                            <span class="soft-badge">{{ auth()->user()->name }}</span>
                            <a href="{{ route('profile.edit') }}" class="soft-cta text-xs px-4 py-2">Profile</a>
                        @endauth
                    </div>
                </header>

                @isset($header)
                    <div class="soft-panel p-6">
                        {{ $header }}
                    </div>
                @endisset

                <div class="flex flex-col gap-6">
                    @if (session('success'))
                        <div class="soft-card p-4 text-sm text-emerald-100">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="soft-card p-4 text-sm text-emerald-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </div>
        <script src="{{ asset('vendor/adminlte/dist/js/jarvis.js') }}" defer></script>
    </body>
</html>

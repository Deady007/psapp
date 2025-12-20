<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('branding.name', config('app.name', 'Laravel')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    </head>
    <body class="font-sans antialiased text-slate-100">
        <div class="app-shell">
            <div class="flex min-h-screen">
                @include('layouts.navigation')

                <div class="flex-1 pt-16 lg:pt-0">
                    @isset($header)
                        <header class="relative overflow-hidden border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(245,158,11,0.18),_transparent_55%)] opacity-70"></div>
                            <div class="relative mx-auto flex min-h-[96px] items-center max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="pb-12">
                        @if (session('success'))
                            <div class="mx-auto max-w-7xl pt-6 sm:px-6 lg:px-8">
                                <div class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 p-4 text-emerald-100 shadow-[0_12px_30px_rgba(16,185,129,0.25)]">
                                    {{ session('success') }}
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mx-auto max-w-7xl pt-6 sm:px-6 lg:px-8">
                                <div class="rounded-2xl border border-rose-400/30 bg-rose-400/10 p-4 text-rose-100 shadow-[0_12px_30px_rgba(244,63,94,0.25)]">
                                    {{ session('error') }}
                                </div>
                            </div>
                        @endif

                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        @stack('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('select[data-enhance="choices"]').forEach((selectEl) => {
                    if (selectEl.dataset.choicesAttached === '1') return;
                    new Choices(selectEl, {
                        searchEnabled: true,
                        shouldSort: false,
                        itemSelectText: '',
                        removeItemButton: false,
                    });
                    selectEl.dataset.choicesAttached = '1';
                });

                if (window.flatpickr) {
                    document.querySelectorAll('input[data-datepicker]').forEach((input) => {
                        window.flatpickr(input, {
                            dateFormat: 'Y-m-d',
                            allowInput: true,
                        });
                    });
                }
            });
        </script>
    </body>
</html>

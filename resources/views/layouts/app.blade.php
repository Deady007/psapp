<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
        <style>
            .choices__inner {
                border-radius: 0.375rem;
                border-color: rgb(209 213 219);
                min-height: 2.5rem;
                padding: 0.4rem 0.75rem;
            }
            .choices__list--single .choices__item {
                color: #111827;
            }
            .choices__list--dropdown {
                border-radius: 0.5rem;
            }
            .flatpickr-input {
                background-color: #fff;
                cursor: pointer;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @if (session('success'))
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-6">
                        <div class="rounded-md border border-green-200 bg-green-50 p-4 text-green-800">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-6">
                        <div class="rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
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

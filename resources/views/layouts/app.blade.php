@extends('adminlte::page')

@section('title', config('branding.name', config('app.name', 'Laravel')))

@isset($header)
    @section('content_header')
        {{ $header }}
    @endsection
@endisset

@section('content')
    <main id="main-content" class="container-fluid px-lg-4">
        <div class="toast-stack" aria-live="polite" aria-atomic="true">
            @if (session('success'))
                <div class="toast-card toast-success" role="status" data-autodismiss="true">
                    <div class="toast-message">{{ session('success') }}</div>
                    <button type="button" class="toast-close" aria-label="{{ __('Close') }}">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="toast-card toast-error" role="status" data-autodismiss="true">
                    <div class="toast-message">{{ session('error') }}</div>
                    <button type="button" class="toast-close" aria-label="{{ __('Close') }}">&times;</button>
                </div>
            @endif
        </div>

        <div class="page-shell">

            {{ $slot }}
        </div>
    </main>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('select[data-enhance="choices"]').forEach((selectEl) => {
                if (selectEl.dataset.choicesAttached === '1') {
                    return;
                }

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

                document.querySelectorAll('input[data-datetimepicker]').forEach((input) => {
                    window.flatpickr(input, {
                        dateFormat: 'Y-m-d H:i',
                        allowInput: true,
                        enableTime: true,
                        time_24hr: true,
                    });
                });
            }
        });
    </script>
@endsection

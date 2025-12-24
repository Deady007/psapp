@extends('adminlte::page')

@section('title', config('branding.name', config('app.name', 'Laravel')))

@isset($header)
    @section('content_header')
        {{ $header }}
    @endsection
@endisset

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('error') }}
        </div>
    @endif

    {{ $slot }}
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

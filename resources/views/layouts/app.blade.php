@extends('adminlte::page')
@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@inject('layoutHelper', 'JeroenNoten\\LaravelAdminLte\\Helpers\\LayoutHelper')

@section('body_data', trim($layoutHelper->makeBodyData().' data-theme="softui" data-density="comfortable" data-motion="1"'))

@php
    $isAdminPage = request()->routeIs('admin.*');
    $isSettingsPage = request()->routeIs('settings.application') || request()->routeIs('profile.*');
    $resolvedBodyClass = trim(($bodyClass ?? '').($isAdminPage ? ' admin-page' : '').($isSettingsPage ? ' settings-page' : ''));
@endphp

@section('title', config('branding.name', config('app.name', 'Laravel')))
@section('classes_body', trim($layoutHelper->makeBodyClasses().' terminal-body '.$resolvedBodyClass))
@section('body_data', trim($layoutHelper->makeBodyData().' data-theme="softui" data-density="comfortable" data-motion="1" data-particles-intensity="med"'))

@section('content_top_nav_right')
@endsection

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

@section('js')
    <script>
        (() => {
            try {
                const theme = localStorage.getItem('gearbox_theme');
                if (theme) {
                    document.documentElement.dataset.theme = theme;
                    if (document.body) {
                        document.body.dataset.theme = theme;
                    }
                }

                const particles = localStorage.getItem('gearbox_particles_intensity');
                if (particles && document.body) {
                    document.body.dataset.particlesIntensity = particles;
                }

                const defaultCustom = {
                    bg: '#0b0f15',
                    surface: '#0f1115',
                    surface2: '#1a1f28',
                    text: '#e2e8f0',
                    accent: '#22d3ee',
                    border: '#3f3f46',
                    radius: '18',
                    blur: '12',
                    glow: '20',
                    radial1: '#00ff75',
                    radial2: '#7cff9c',
                    radial3: '#2dd4bf',
                    gridOpacity: '0.25',
                    particlesOpacity: '0.55',
                    earthVisible: true,
                    earthColor: '#22d3ee',
                    earthOpacity: '0.6',
                    earthScale: '1',
                };

                const rawCustom = localStorage.getItem('gearbox_custom_ui');
                if (rawCustom) {
                    const cfg = { ...defaultCustom, ...JSON.parse(rawCustom) };
                    const root = document.documentElement;
                    const glowPx = Number(cfg.glow) || Number(defaultCustom.glow);
                    const radiusPx = Number(cfg.radius) || Number(defaultCustom.radius);
                    const blurPx = Number(cfg.blur) || Number(defaultCustom.blur);
                    const accent = cfg.accent || defaultCustom.accent;

                    root.style.setProperty('--custom-bg', cfg.bg);
                    root.style.setProperty('--custom-surface', cfg.surface);
                    root.style.setProperty('--custom-surface-2', cfg.surface2);
                    root.style.setProperty('--custom-text', cfg.text);
                    root.style.setProperty('--custom-muted', `color-mix(in srgb, ${cfg.text} 70%, transparent)`);
                    root.style.setProperty('--custom-border', cfg.border);
                    root.style.setProperty('--custom-accent', accent);
                    root.style.setProperty('--custom-accent-2', accent);
                    root.style.setProperty('--custom-focus', accent);
                    root.style.setProperty('--custom-radius', `${radiusPx}px`);
                    root.style.setProperty('--custom-blur', `${blurPx}px`);
                    root.style.setProperty('--custom-glow', `0 0 ${glowPx}px color-mix(in srgb, ${accent} 55%, transparent)`);
                    root.style.setProperty('--custom-radial-1', cfg.radial1);
                    root.style.setProperty('--custom-radial-2', cfg.radial2);
                    root.style.setProperty('--custom-radial-3', cfg.radial3);
                    root.style.setProperty('--custom-before-1', cfg.radial1);
                    root.style.setProperty('--custom-before-2', cfg.radial2);
                    root.style.setProperty('--custom-before-3', cfg.radial3);
                    root.style.setProperty('--custom-grid-opacity', cfg.gridOpacity);
                    root.style.setProperty('--custom-particles', cfg.particlesOpacity);
                    root.style.setProperty('--custom-earth', cfg.earthColor);
                    root.style.setProperty('--custom-earth-opacity', cfg.earthOpacity);
                    root.style.setProperty('--custom-earth-scale', cfg.earthScale);
                    root.style.setProperty('--custom-earth-visible', cfg.earthVisible ? 1 : 0);
                }
            } catch (error) {
                console.warn('Theme bootstrap failed', error);
            }
        })();
    </script>
    <script>
        window.appUi = {
            enhance(scope = document) {
                const $ = window.jQuery;

                if ($ && $.fn.select2) {
                    $(scope).find('select[data-control="select2"]').each(function () {
                        const $select = $(this);
                        if ($select.hasClass('select2-hidden-accessible')) {
                            return;
                        }

                        const allowClear = ['1', 'true', true, 1].includes($select.data('allowClear'));
                        const placeholder = $select.data('placeholder') || $select.attr('placeholder') || undefined;

                        $select.select2({
                            width: '100%',
                            placeholder,
                            allowClear,
                        });
                    });
                }

                if ($ && $.fn.DataTable) {
                    $(scope).find('table[data-table="datatable"]').each(function () {
                        if ($.fn.DataTable.isDataTable(this)) {
                            return;
                        }

                        $(this).DataTable({
                            responsive: true,
                            autoWidth: false,
                            paging: false,
                            info: false,
                            lengthChange: false,
                            searching: true,
                            order: [],
                        });
                    });
                }

                if (window.flatpickr) {
                    scope.querySelectorAll('input[data-datepicker]').forEach((input) => {
                        if (input._flatpickr) {
                            return;
                        }

                        window.flatpickr(input, {
                            dateFormat: 'Y-m-d',
                            allowInput: true,
                        });
                    });

                    scope.querySelectorAll('input[data-datetimepicker]').forEach((input) => {
                        if (input._flatpickr) {
                            return;
                        }

                        window.flatpickr(input, {
                            dateFormat: 'Y-m-d H:i',
                            allowInput: true,
                            enableTime: true,
                            time_24hr: true,
                        });
                    });
                }

                if ($ && $.fn.summernote) {
                    $(scope).find('textarea[data-richtext=\"summernote\"]').each(function () {
                        const $textarea = $(this);
                        if ($textarea.data('summernote')) {
                            return;
                        }

                        $textarea.summernote({
                            height: $textarea.data('height') || 220,
                            toolbar: [
                                ['style', ['bold', 'italic', 'underline', 'clear']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['insert', ['link']],
                                ['view', ['fullscreen', 'codeview']],
                            ],
                        });
                    });
                }

                if (window.Swal && scope.querySelectorAll) {
                    scope.querySelectorAll('form[data-confirm]').forEach((form) => {
                        if (form.dataset.confirmAttached === '1') {
                            return;
                        }

                        form.addEventListener('submit', (event) => {
                            if (form.dataset.confirmed === '1') {
                                return;
                            }

                            event.preventDefault();
                            const message = form.dataset.confirm || '{{ __('Are you sure?') }}';
                            const confirmButtonText = form.dataset.confirmButton || '{{ __('Yes, delete it') }}';
                            const cancelButtonText = form.dataset.cancelButton || '{{ __('Cancel') }}';

                            window.Swal.fire({
                                title: message,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc3545',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText,
                                cancelButtonText,
                                focusCancel: true,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.dataset.confirmed = '1';
                                    form.submit();
                                }
                            });
                        });

                        form.dataset.confirmAttached = '1';
                    });
                }
            },
        };

        document.addEventListener('DOMContentLoaded', () => {
            window.appUi.enhance();
        });

        @if (session('error_details'))
        console.error('Request failed', @json(session('error_details')));
        @elseif (session('error'))
        console.error('Request failed', @json(session('error')));
        @endif
    </script>
@endsection

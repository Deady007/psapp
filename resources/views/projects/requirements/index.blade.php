<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Requirements') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('projects.requirements.create', $project) }}" class="btn btn-primary mr-2">
                    <i class="fas fa-plus mr-1"></i>
                    {{ __('Add Requirements') }}
                </a>
                <a href="{{ route('projects.requirements.import', $project) }}" class="btn btn-outline-primary mr-2">
                    <i class="fas fa-file-upload mr-1"></i>
                    {{ __('Import from Transcript') }}
                </a>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Project') }}
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $rfpStatusLabels = [
            'queued' => __('Queued'),
            'processing' => __('Generating'),
            'completed' => __('Ready'),
            'failed' => __('Failed'),
        ];
        $rfpStatusStyles = [
            'queued' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
        ];
        $rfpStatus = $rfpDocument?->status;
        $rfpStatusLabel = $rfpStatus && isset($rfpStatusLabels[$rfpStatus]) ? $rfpStatusLabels[$rfpStatus] : __('Not generated');
        $rfpStatusStyle = $rfpStatus && isset($rfpStatusStyles[$rfpStatus]) ? $rfpStatusStyles[$rfpStatus] : 'secondary';
        $isRfpRunning = $rfpStatus && in_array($rfpStatus, ['queued', 'processing'], true);
    @endphp

    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <div class="text-muted small">{{ __('RFP status') }}</div>
                <div class="d-flex align-items-center flex-wrap">
                    <span class="badge badge-{{ $rfpStatusStyle }} mr-2">{{ $rfpStatusLabel }}</span>
                    @if ($rfpDocument?->updated_at)
                        <span class="text-muted small">
                            {{ __('Updated') }} {{ $rfpDocument->updated_at->toDateTimeString() }}
                        </span>
                    @endif
                </div>
                @if ($rfpDocument?->status === 'failed' && $rfpDocument->error_message)
                    <div class="text-danger small mt-2">{{ $rfpDocument->error_message }}</div>
                @endif
            </div>
            <div class="mt-3 mt-sm-0">
                <form method="POST" action="{{ route('projects.requirements.rfp.store', $project) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" @disabled($isRfpRunning)>
                        <i class="fas fa-file-alt mr-1"></i>
                        {{ __('Generate RFP') }}
                    </button>
                </form>
                @if ($rfpDocument?->status === 'completed')
                    <a href="{{ route('projects.requirements.rfp.download', [$project, $rfpDocument]) }}" class="btn btn-outline-success ml-2">
                        <i class="fas fa-download mr-1"></i>
                        {{ __('Download') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    @include('projects.partials.modules-nav', ['project' => $project])

    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h3 class="card-title">{{ __('Requirement list') }}</h3>
                @if ($modules->isNotEmpty())
                    <div class="text-muted small">
                        {{ __('Select a module to view its requirements.') }}
                    </div>
                @endif
            </div>
            @if ($modules->isNotEmpty())
                <ul class="nav nav-pills mt-3" id="requirements-module-tabs">
                    @foreach ($modules as $module)
                        <li class="nav-item">
                            <a href="{{ route('projects.requirements.index', [$project, 'module' => $module->module_name]) }}"
                                class="nav-link js-module-tab @if ($selectedModule === $module->module_name) active @endif"
                                data-module="{{ $module->module_name }}">
                                {{ $module->module_name }}
                                <span class="badge badge-light ml-1">{{ $module->total }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if ($modules->isEmpty())
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-title mb-2">{{ __('No requirements captured yet') }}</div>
                    <p class="text-muted mb-3">{{ __('Add requirements or import a kickoff transcript to get started.') }}</p>
                    <div class="d-flex flex-wrap justify-content-center">
                        <a href="{{ route('projects.requirements.create', $project) }}" class="btn btn-primary mr-2 mb-2">
                            <i class="fas fa-plus mr-1"></i>
                            {{ __('Add Requirements') }}
                        </a>
                        <a href="{{ route('projects.requirements.import', $project) }}" class="btn btn-outline-primary mb-2">
                            <i class="fas fa-file-upload mr-1"></i>
                            {{ __('Import Transcript') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div id="requirements-panel">
                @include('projects.requirements.partials.list', [
                    'project' => $project,
                    'requirements' => $requirements,
                    'selectedModule' => $selectedModule,
                ])
            </div>
        @endif
    </div>

    @if ($modules->isNotEmpty())
        @push('js')
            <script>
                $(function () {
                    const $panel = $('#requirements-panel');
                    const $tabs = $('#requirements-module-tabs');

                    if (! $panel.length || ! $tabs.length) {
                        return;
                    }

                    const errorText = @json(__('Unable to load requirements right now. Please try again.'));
                    const loadingMarkup = `
                        <div class="card-body">
                            <div class="skeleton mb-3" style="height: 18px;"></div>
                            <div class="skeleton mb-2" style="height: 14px;"></div>
                            <div class="skeleton mb-2" style="height: 14px;"></div>
                            <div class="skeleton" style="height: 14px; width: 60%;"></div>
                        </div>
                    `;
                    const errorMarkup = `<div class="card-body"><div class="alert alert-danger mb-0">${errorText}</div></div>`;

                    const setActiveTab = (moduleName) => {
                        $tabs.find('.nav-link').removeClass('active');
                        $tabs.find('.nav-link').filter(function () {
                            return $(this).data('module') === moduleName;
                        }).addClass('active');
                    };

                    const getModuleFromUrl = (url) => {
                        const parsed = new URL(url, window.location.origin);
                        return parsed.searchParams.get('module');
                    };

                    const loadModule = (url, moduleName = null) => {
                        $panel.html(loadingMarkup);

                        $.ajax({
                            url: url,
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            success: function (html) {
                                $panel.html(html);
                                window.history.replaceState({}, '', url);
                                const resolvedModule = moduleName || getModuleFromUrl(url);
                                if (resolvedModule) {
                                    setActiveTab(resolvedModule);
                                }
                            },
                            error: function () {
                                $panel.html(errorMarkup);
                            }
                        });
                    };

                    $tabs.on('click', '.js-module-tab', function (e) {
                        e.preventDefault();
                        const $link = $(this);
                        loadModule($link.attr('href'), $link.data('module'));
                    });

                    $panel.on('click', '.pagination a', function (e) {
                        e.preventDefault();
                        loadModule($(this).attr('href'));
                    });
                });
            </script>
        @endpush
    @endif
</x-app-layout>

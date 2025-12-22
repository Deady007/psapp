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
                <p class="text-muted mb-0">{{ __('No requirements found.') }}</p>
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

                    const loadingText = @json(__('Loading requirements...'));
                    const errorText = @json(__('Unable to load requirements right now. Please try again.'));
                    const loadingMarkup = `<div class="card-body text-center text-muted py-5"><i class="fas fa-spinner fa-spin mr-1"></i>${loadingText}</div>`;
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

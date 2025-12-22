<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Import Requirements') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ $backUrl ?? route('projects.requirements.index', $project) }}" class="btn btn-outline-secondary">
                    {{ $backLabel ?? __('Back to Requirements') }}
                </a>
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Upload transcript') }}</h3>
            <div class="card-tools text-muted">
                {{ __('Upload a .txt transcript to generate requirement drafts.') }}
                @if (($source ?? null) === 'kickoff')
                    <span class="ml-2 badge badge-info">{{ __('Kick-off transcript') }}</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('projects.requirements.import.preview', $project) }}" enctype="multipart/form-data" id="requirements-transcript-form">
                @csrf
                @if (! empty($source))
                    <input type="hidden" name="source" value="{{ $source }}">
                @endif

                <div class="form-group">
                    <x-input-label for="transcript" :value="__('Transcript (.txt)')" />
                    <input id="transcript" name="transcript" type="file" class="form-control-file" accept=".txt,text/plain" required>
                    <x-input-error class="mt-2" :messages="$errors->get('transcript')" />
                </div>

                <div class="form-group">
                    <x-input-label :value="__('Analysis Mode')" />
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="analysis-fast" name="analysis_mode" value="fast" @checked($analysisMode === 'fast')>
                        <label class="custom-control-label" for="analysis-fast">
                            {{ __('Fast') }} <span class="text-muted small">- {{ __('quicker results, fewer passes') }}</span>
                        </label>
                    </div>
                    <div class="custom-control custom-radio mt-2">
                        <input class="custom-control-input" type="radio" id="analysis-deep" name="analysis_mode" value="deep" @checked($analysisMode === 'deep')>
                        <label class="custom-control-label" for="analysis-deep">
                            {{ __('Deep') }} <span class="text-muted small">- {{ __('slower, more thorough extraction') }}</span>
                        </label>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('analysis_mode')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2" id="requirements-transcript-submit">
                        <span class="js-upload-label">{{ __('Analyze Transcript') }}</span>
                    </x-primary-button>
                    <a href="{{ $backUrl ?? route('projects.requirements.index', $project) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if (count($drafts) > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Preview requirements') }}</h3>
                <div class="card-tools text-muted">
                    {{ __('Review, edit, and select the requirements to import.') }}
                    @isset($transcriptName)
                        <span class="ml-2">- {{ __('Transcript') }}: {{ $transcriptName }}</span>
                    @endisset
                </div>
            </div>
            <form method="POST" action="{{ route('projects.requirements.import.store', $project) }}">
                @csrf

                <div class="card-body">
                    <x-input-error class="mb-3" :messages="$errors->get('requirements')" />

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">{{ __('Import') }}</th>
                                    <th>{{ __('Module') }}</th>
                                    <th>{{ __('Page') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Details') }}</th>
                                    <th>{{ __('Priority') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($drafts as $index => $draft)
                                    @php
                                        $selected = array_key_exists('selected', $draft) ? (bool) $draft['selected'] : true;
                                        $moduleName = $draft['module_name'] ?? '';
                                        $pageName = $draft['page_name'] ?? '';
                                        $title = $draft['title'] ?? '';
                                        $details = $draft['details'] ?? '';
                                        $priority = $draft['priority'] ?? 'medium';
                                        $status = $draft['status'] ?? 'todo';
                                    @endphp
                                    <tr class="requirement-row">
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input"
                                                    id="requirement-select-{{ $index }}"
                                                    name="requirements[{{ $index }}][selected]"
                                                    value="1"
                                                    @checked($selected)>
                                                <label class="custom-control-label" for="requirement-select-{{ $index }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <x-text-input name="requirements[{{ $index }}][module_name]" type="text" :value="$moduleName" required />
                                            <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.module_name')" />
                                        </td>
                                        <td>
                                            <x-text-input name="requirements[{{ $index }}][page_name]" type="text" :value="$pageName" />
                                            <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.page_name')" />
                                        </td>
                                        <td>
                                            <x-text-input name="requirements[{{ $index }}][title]" type="text" :value="$title" required />
                                            <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.title')" />
                                        </td>
                                        <td style="min-width: 240px;">
                                            <textarea name="requirements[{{ $index }}][details]" rows="2" class="form-control">{{ $details }}</textarea>
                                            <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.details')" />
                                        </td>
                                        <td>
                                            <select name="requirements[{{ $index }}][priority]" data-enhance="choices" class="form-control" required>
                                                @foreach ($priorities as $priorityOption)
                                                    <option value="{{ $priorityOption }}" @selected($priority === $priorityOption)>
                                                        {{ __($priorityOption) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.priority')" />
                                        </td>
                                        <td>
                                            <select name="requirements[{{ $index }}][status]" data-enhance="choices" class="form-control" required>
                                                @foreach ($statuses as $statusOption)
                                                    <option value="{{ $statusOption }}" @selected($status === $statusOption)>
                                                        {{ __($statusOption) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.status')" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Approve & Import') }}</x-primary-button>
                    <a href="{{ $backUrl ?? route('projects.requirements.index', $project) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    @endif

    @push('js')
        <script>
            $(function () {
                $('#requirements-transcript-form').on('submit', function () {
                    const $button = $('#requirements-transcript-submit');
                    $button.prop('disabled', true);
                    $button.find('.js-upload-label').text(@json(__('Analyzing...')));
                });
            });
        </script>
    @endpush
</x-app-layout>

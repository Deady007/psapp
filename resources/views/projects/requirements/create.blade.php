<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('New Requirements') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('projects.requirements.index', $project) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Requirements') }}
                </a>
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    @php
        $requirements = old('requirements');

        if (! is_array($requirements) || $requirements === []) {
            $requirements = [[
                'module_name' => '',
                'page_name' => '',
                'title' => '',
                'details' => '',
                'priority' => 'medium',
                'status' => 'todo',
            ]];
        }

        $requirementIndexes = array_map('intval', array_keys($requirements));
        $nextIndex = $requirementIndexes === [] ? 0 : (max($requirementIndexes) + 1);
    @endphp

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Add requirements') }}</h3>
            <div class="card-tools text-muted">
                {{ __('New rows copy module, page, priority, and status from the previous row.') }}
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('projects.requirements.store', $project) }}">
                @csrf

                <x-input-error class="mb-3" :messages="$errors->get('requirements')" />

                <div id="requirements-rows" data-next-index="{{ $nextIndex }}">
                    @foreach ($requirements as $index => $requirement)
                        @php
                            $moduleName = $requirement['module_name'] ?? '';
                            $pageName = $requirement['page_name'] ?? '';
                            $title = $requirement['title'] ?? '';
                            $details = $requirement['details'] ?? '';
                            $priority = $requirement['priority'] ?? 'medium';
                            $status = $requirement['status'] ?? 'todo';
                        @endphp

                        <div class="border rounded p-3 mb-3 requirement-row" data-index="{{ $index }}">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="font-weight-bold">
                                    {{ __('Requirement') }} <span data-row-number>{{ $loop->iteration }}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger js-remove-requirement">
                                    {{ __('Remove') }}
                                </button>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <x-input-label for="requirements_{{ $index }}_module_name" :value="__('Module Name')" />
                                    <x-text-input id="requirements_{{ $index }}_module_name" name="requirements[{{ $index }}][module_name]" type="text" :value="$moduleName" required data-field="module_name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.module_name')" />
                                </div>

                                <div class="form-group col-md-3">
                                    <x-input-label for="requirements_{{ $index }}_page_name" :value="__('Page Name')" />
                                    <x-text-input id="requirements_{{ $index }}_page_name" name="requirements[{{ $index }}][page_name]" type="text" :value="$pageName" data-field="page_name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.page_name')" />
                                </div>

                                <div class="form-group col-md-3">
                                    <x-input-label for="requirements_{{ $index }}_priority" :value="__('Priority')" />
                                    <select id="requirements_{{ $index }}_priority" name="requirements[{{ $index }}][priority]" data-control="select2" class="form-control" required data-field="priority">
                                        @foreach ($priorities as $priorityOption)
                                            <option value="{{ $priorityOption }}" @selected($priority === $priorityOption)>
                                                {{ __($priorityOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.priority')" />
                                </div>

                                <div class="form-group col-md-3">
                                    <x-input-label for="requirements_{{ $index }}_status" :value="__('Status')" />
                                    <select id="requirements_{{ $index }}_status" name="requirements[{{ $index }}][status]" data-control="select2" class="form-control" required data-field="status">
                                        @foreach ($statuses as $statusOption)
                                            <option value="{{ $statusOption }}" @selected($status === $statusOption)>
                                                {{ __($statusOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.status')" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <x-input-label for="requirements_{{ $index }}_title" :value="__('Title')" />
                                    <x-text-input id="requirements_{{ $index }}_title" name="requirements[{{ $index }}][title]" type="text" :value="$title" required data-field="title" />
                                    <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.title')" />
                                </div>

                                <div class="form-group col-md-8">
                                    <x-input-label for="requirements_{{ $index }}_details" :value="__('Details')" />
                                    <textarea id="requirements_{{ $index }}_details" name="requirements[{{ $index }}][details]" rows="2" class="form-control" data-field="details" data-richtext="summernote">{{ $details }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('requirements.' . $index . '.details')" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <button type="button" class="btn btn-outline-primary js-add-requirement">
                        <i class="fas fa-plus mr-1"></i>
                        {{ __('Add another requirement') }}
                    </button>
                    <div class="text-muted small mt-2 mt-sm-0">
                        {{ __('Add all rows then save once.') }}
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Save Requirements') }}</x-primary-button>
                    <a href="{{ route('projects.requirements.index', $project) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <template id="requirement-row-template">
        <div class="border rounded p-3 mb-3 requirement-row" data-index="__INDEX__">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="font-weight-bold">
                    {{ __('Requirement') }} <span data-row-number></span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger js-remove-requirement">
                    {{ __('Remove') }}
                </button>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <x-input-label for="requirements___INDEX___module_name" :value="__('Module Name')" />
                    <x-text-input id="requirements___INDEX___module_name" name="requirements[__INDEX__][module_name]" type="text" value="" required data-field="module_name" />
                </div>

                <div class="form-group col-md-3">
                    <x-input-label for="requirements___INDEX___page_name" :value="__('Page Name')" />
                    <x-text-input id="requirements___INDEX___page_name" name="requirements[__INDEX__][page_name]" type="text" value="" data-field="page_name" />
                </div>

                <div class="form-group col-md-3">
                    <x-input-label for="requirements___INDEX___priority" :value="__('Priority')" />
                    <select id="requirements___INDEX___priority" name="requirements[__INDEX__][priority]" data-control="select2" class="form-control" required data-field="priority">
                        @foreach ($priorities as $priorityOption)
                            <option value="{{ $priorityOption }}" @selected($priorityOption === 'medium')>
                                {{ __($priorityOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <x-input-label for="requirements___INDEX___status" :value="__('Status')" />
                    <select id="requirements___INDEX___status" name="requirements[__INDEX__][status]" data-control="select2" class="form-control" required data-field="status">
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption }}" @selected($statusOption === 'todo')>
                                {{ __($statusOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <x-input-label for="requirements___INDEX___title" :value="__('Title')" />
                    <x-text-input id="requirements___INDEX___title" name="requirements[__INDEX__][title]" type="text" value="" required data-field="title" />
                </div>

                <div class="form-group col-md-8">
                    <x-input-label for="requirements___INDEX___details" :value="__('Details')" />
                    <textarea id="requirements___INDEX___details" name="requirements[__INDEX__][details]" rows="2" class="form-control" data-field="details" data-richtext="summernote"></textarea>
                </div>
            </div>
        </div>
    </template>

    @push('js')
        <script>
            $(function () {
                const $rows = $('#requirements-rows');
                const templateHtml = $('#requirement-row-template').html();
                let nextIndex = parseInt($rows.data('next-index'), 10) || 0;

                const enhanceControls = (scope) => {
                    const target = scope && scope[0] ? scope[0] : scope || document;
                    if (window.appUi && target) {
                        window.appUi.enhance(target);
                    }
                };

                const syncRowNumbers = () => {
                    const $allRows = $rows.find('.requirement-row');

                    $allRows.each(function (index) {
                        $(this).find('[data-row-number]').text(index + 1);
                    });

                    $allRows.find('.js-remove-requirement').prop('disabled', $allRows.length === 1);
                };

                const copyPreviousValues = ($row) => {
                    const $previousRow = $rows.find('.requirement-row').last();
                    if (! $previousRow.length) {
                        return;
                    }

                    ['module_name', 'page_name', 'priority', 'status'].forEach((field) => {
                        const value = $previousRow.find(`[data-field="${field}"]`).val();
                        $row.find(`[data-field="${field}"]`).val(value);
                    });

                    $row.find('[data-field="title"]').val('');
                    $row.find('[data-field="details"]').val('');
                };

                const addRow = () => {
                    const index = nextIndex;
                    nextIndex += 1;

                    const $row = $(templateHtml.replace(/__INDEX__/g, index));
                    copyPreviousValues($row);
                    $rows.append($row);
                    enhanceControls($row);
                    syncRowNumbers();
                };

                $rows.on('click', '.js-remove-requirement', function () {
                    if ($rows.find('.requirement-row').length === 1) {
                        return;
                    }

                    $(this).closest('.requirement-row').remove();
                    syncRowNumbers();
                });

                $('.js-add-requirement').on('click', function () {
                    addRow();
                });

                syncRowNumbers();
                enhanceControls($rows);
            });
        </script>
    @endpush
</x-app-layout>

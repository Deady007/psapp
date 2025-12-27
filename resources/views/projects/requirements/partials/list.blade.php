@php($priorityClasses = ['low' => 'secondary', 'medium' => 'info', 'high' => 'danger'])
@php($statusClasses = ['todo' => 'secondary', 'in_progress' => 'warning', 'done' => 'success'])

<div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between">
        <div class="text-muted">
            {{ __('Showing') }} {{ $requirements->firstItem() ?? 0 }}-{{ $requirements->lastItem() ?? 0 }} {{ __('of') }} {{ $requirements->total() }}
        </div>
        @if ($selectedModule)
            <div class="text-muted small">
                {{ __('Module') }}: {{ $selectedModule }}
            </div>
        @endif
    </div>
</div>

@if ($requirements->count() === 0)
    <div class="card-body pt-0">
        <p class="text-muted mb-0">
            {{ $selectedModule ? __('No requirements found for this module.') : __('No requirements found.') }}
        </p>
    </div>
@else
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap" data-table="datatable">
            <thead>
                <tr>
                    <th>{{ __('Module / Page') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Priority') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requirements as $requirement)
                    <tr>
                        <td>
                            <div class="font-weight-bold">{{ $requirement->module_name }}</div>
                            <div class="text-muted small">{{ $requirement->page_name ?: '-' }}</div>
                        </td>
                        <td>
                            <div class="font-weight-bold">{{ $requirement->title }}</div>
                            <div class="text-muted small">{{ $requirement->details ?: '-' }}</div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $priorityClasses[$requirement->priority] ?? 'secondary' }}">
                                {{ __($requirement->priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $statusClasses[$requirement->status] ?? 'secondary' }}">
                                {{ __($requirement->status) }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('projects.requirements.edit', [$project, $requirement]) }}" class="btn btn-sm btn-outline-secondary">
                                {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('projects.requirements.destroy', [$project, $requirement]) }}" class="d-inline" data-confirm="{{ __('Delete this requirement?') }}" data-confirm-button="{{ __('Yes, delete it') }}" data-cancel-button="{{ __('Cancel') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $requirements->links() }}
    </div>
@endif

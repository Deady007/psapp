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
                    {{ __('New Requirement') }}
                </a>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Project') }}
                </a>
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    @php($priorityClasses = ['low' => 'secondary', 'medium' => 'info', 'high' => 'danger'])
    @php($statusClasses = ['todo' => 'secondary', 'in_progress' => 'warning', 'done' => 'success'])

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Requirement list') }}</h3>
            <div class="card-tools text-muted">
                {{ __('Showing') }} {{ $requirements->firstItem() ?? 0 }}-{{ $requirements->lastItem() ?? 0 }} {{ __('of') }} {{ $requirements->total() }}
            </div>
        </div>

        @if ($requirements->count() === 0)
            <div class="card-body">
                <p class="text-muted mb-0">{{ __('No requirements found.') }}</p>
            </div>
        @else
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
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
                                    <form method="POST" action="{{ route('projects.requirements.destroy', [$project, $requirement]) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this requirement?') }}')">
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
    </div>
</x-app-layout>

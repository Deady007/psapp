<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-lg-8">
                <h1 class="m-0">{{ $project->name }}</h1>
                <div class="text-muted">{{ __('Project overview') }}</div>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary mr-2">
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this project?') }}')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>{{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    <div class="row">
        <div class="col-lg-4 col-md-6">
            <x-adminlte-info-box
                title="{{ __('Kick-off') }}"
                text="{{ $project->kickoff?->status ? __($project->kickoff->status) : __('Not scheduled') }}"
                icon="fas fa-phone"
                theme="info"
                url="{{ route('projects.kickoffs.show', $project) }}"
            />
        </div>
        <div class="col-lg-4 col-md-6">
            <x-adminlte-info-box
                title="{{ __('Requirements') }}"
                text="{{ $project->requirements_count }}"
                icon="fas fa-clipboard-list"
                theme="success"
                url="{{ route('projects.requirements.index', $project) }}"
            />
        </div>
        <div class="col-lg-4 col-md-6">
            <x-adminlte-info-box
                title="{{ __('Products') }}"
                text="{{ $project->products_count }}"
                icon="fas fa-cubes"
                theme="secondary"
                url="{{ route('projects.edit', $project) }}"
            />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Details') }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted">{{ __('Customer') }}</th>
                                <td>
                                    <a href="{{ route('customers.show', $project->customer) }}">
                                        {{ $project->customer->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ __('Products') }}</th>
                                <td>
                                    @forelse ($project->products as $product)
                                        <span class="badge badge-info mr-1">{{ $product->name }}</span>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ __('Code') }}</th>
                                <td>{{ $project->code ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ __('Status') }}</th>
                                <td>
                                    @if ($project->status === 'active')
                                        <span class="badge badge-success">{{ __('active') }}</span>
                                    @elseif ($project->status === 'on_hold')
                                        <span class="badge badge-warning">{{ __('on_hold') }}</span>
                                    @elseif ($project->status === 'completed')
                                        <span class="badge badge-info">{{ __('completed') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('draft') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">{{ __('Description') }}</th>
                                <td style="white-space: pre-line;">{{ $project->description ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Timeline') }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted">{{ __('Start Date') }}</div>
                        <div class="font-weight-bold">{{ $project->start_date?->toDateString() ?: '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">{{ __('Due Date') }}</div>
                        <div class="font-weight-bold">{{ $project->due_date?->toDateString() ?: '-' }}</div>
                    </div>
                    <div class="text-muted">
                        {{ __('Share progress, unblock tasks, and keep stakeholders aligned.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h3 class="card-title mb-0">{{ __('Project workspace') }}</h3>
            <span class="text-muted small">{{ __('Quick access to kickoffs, requirements, and documents') }}</span>
        </div>
        <div class="card-body p-0">
            <ul class="nav nav-tabs px-3 pt-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#project-kickoff" role="tab">
                        {{ __('Kick-off') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#project-requirements" role="tab">
                        {{ __('Requirements') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#project-documents" role="tab">
                        {{ __('Drive Documents') }}
                    </a>
                </li>
            </ul>
            <div class="tab-content p-3">
                <div class="tab-pane fade show active" id="project-kickoff" role="tabpanel">
                    @if (! $project->kickoff)
                        <div class="empty-state">
                            <div class="empty-title mb-2">{{ __('Kick-off not planned') }}</div>
                            <p class="text-muted mb-3">{{ __('Create the kick-off plan and share the agenda with stakeholders.') }}</p>
                            <a href="{{ route('projects.kickoffs.plan', $project) }}" class="btn btn-primary">
                                <i class="fas fa-clipboard-check mr-1"></i>
                                {{ __('Plan Kick-off') }}
                            </a>
                        </div>
                    @else
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div>
                                <div class="text-muted small">{{ __('Status') }}</div>
                                <div class="font-weight-bold">{{ __($project->kickoff->status) }}</div>
                                <div class="text-muted small">
                                    {{ __('Scheduled') }}: {{ $project->kickoff->scheduled_at?->format('Y-m-d H:i') ?: __('Not scheduled') }}
                                </div>
                            </div>
                            <a href="{{ route('projects.kickoffs.show', $project) }}" class="btn btn-outline-secondary">
                                {{ __('Open kick-off') }}
                            </a>
                        </div>
                        <div class="text-muted small">
                            {{ __('Keep stakeholders aligned with agendas, notes, and outcomes.') }}
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="project-requirements" role="tabpanel">
                    @if ($recentRequirements->isEmpty())
                        <div class="empty-state">
                            <div class="empty-title mb-2">{{ __('No requirements captured') }}</div>
                            <p class="text-muted mb-3">{{ __('Add requirements or import from a kickoff transcript.') }}</p>
                            <a href="{{ route('projects.requirements.create', $project) }}" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i>
                                {{ __('Add Requirements') }}
                            </a>
                        </div>
                    @else
                        @php($priorityClasses = ['low' => 'secondary', 'medium' => 'info', 'high' => 'danger'])
                        @php($statusClasses = ['todo' => 'secondary', 'in_progress' => 'warning', 'done' => 'success'])
                        <ul class="list-group list-group-flush">
                            @foreach ($recentRequirements as $requirement)
                                <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">{{ $requirement->title }}</div>
                                        <div class="text-muted small">{{ $requirement->module_name }}</div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-{{ $priorityClasses[$requirement->priority] ?? 'secondary' }} mr-2">
                                            {{ __($requirement->priority) }}
                                        </span>
                                        <span class="badge badge-{{ $statusClasses[$requirement->status] ?? 'secondary' }}">
                                            {{ __($requirement->status) }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('projects.requirements.index', $project) }}" class="btn btn-outline-secondary">
                                {{ __('View all requirements') }}
                            </a>
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="project-documents" role="tabpanel">
                    @if ($recentDocuments->isEmpty())
                        <div class="empty-state">
                            <div class="empty-title mb-2">{{ __('No documents yet') }}</div>
                            <p class="text-muted mb-3">{{ __('Upload files to keep approvals and versions organized.') }}</p>
                            <a href="{{ route('projects.drive-documents.index', $project) }}" class="btn btn-primary">
                                <i class="fas fa-upload mr-1"></i>
                                {{ __('Open Drive Documents') }}
                            </a>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($recentDocuments as $document)
                                <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                                    <div>
                                        <div class="font-weight-bold">{{ $document->name }}</div>
                                        <div class="text-muted small">
                                            {{ $document->received_from ?: __('Source unknown') }}
                                            @if ($document->received_at)
                                                · {{ $document->received_at->format('Y-m-d') }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge badge-light">{{ __('File') }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('projects.drive-documents.index', $project) }}" class="btn btn-outline-secondary">
                                {{ __('View drive documents') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('projects.index') }}" class="btn btn-link">
        {{ __('Back to Projects') }}
    </a>
</x-app-layout>

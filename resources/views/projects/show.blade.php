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

    <a href="{{ route('projects.index') }}" class="btn btn-link">
        {{ __('Back to Projects') }}
    </a>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-lg-8">
                <h1 class="m-0">{{ __('Kick-off Call') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                @if ($project->kickoff)
                    <a href="{{ route('projects.kickoffs.edit', $project) }}" class="btn btn-outline-secondary mr-2">
                        {{ __('Edit') }}
                    </a>
                    <form method="POST" action="{{ route('projects.kickoffs.destroy', $project) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this kick-off?') }}')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ __('Delete') }}</x-danger-button>
                    </form>
                @else
                    <a href="{{ route('projects.kickoffs.create', $project) }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        {{ __('Schedule Kick-off') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    @if (! $project->kickoff)
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-3">{{ __('No kick-off has been scheduled yet.') }}</p>
                <a href="{{ route('projects.kickoffs.create', $project) }}" class="btn btn-primary">
                    <i class="fas fa-calendar-plus mr-1"></i>
                    {{ __('Schedule Kick-off') }}
                </a>
            </div>
        </div>
    @else
        @php($statusClasses = ['draft' => 'secondary', 'scheduled' => 'info', 'completed' => 'success'])
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Kick-off Details') }}</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-muted">{{ __('Purchase Order') }}</th>
                                    <td>{{ $project->kickoff->purchase_order_number ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Status') }}</th>
                                    <td>
                                        <span class="badge badge-{{ $statusClasses[$project->kickoff->status] ?? 'secondary' }}">
                                            {{ __($project->kickoff->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Meeting Mode') }}</th>
                                    <td>{{ $project->kickoff->meeting_mode ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Stakeholders') }}</th>
                                    <td style="white-space: pre-line;">{{ $project->kickoff->stakeholders ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Requirements Summary') }}</th>
                                    <td style="white-space: pre-line;">{{ $project->kickoff->requirements_summary ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Timeline Summary') }}</th>
                                    <td style="white-space: pre-line;">{{ $project->kickoff->timeline_summary ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Notes') }}</th>
                                    <td style="white-space: pre-line;">{{ $project->kickoff->notes ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Schedule') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted">{{ __('Kick-off Date') }}</div>
                            <div class="font-weight-bold">
                                {{ $project->kickoff->scheduled_at?->format('Y-m-d H:i') ?: '-' }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted">{{ __('Products') }}</div>
                            <div>
                                @forelse ($project->products as $product)
                                    <span class="badge badge-info mr-1">{{ $product->name }}</span>
                                @empty
                                    -
                                @endforelse
                            </div>
                        </div>
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary btn-sm">
                            {{ __('Edit Products') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <a href="{{ route('projects.show', $project) }}" class="btn btn-link">
        {{ __('Back to Project') }}
    </a>
</x-app-layout>

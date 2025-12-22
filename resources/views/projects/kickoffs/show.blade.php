<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-lg-8">
                <h1 class="m-0">{{ __('Kick-off Call') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                @if (! $project->kickoff)
                    <a href="{{ route('projects.kickoffs.plan', $project) }}" class="btn btn-primary">
                        <i class="fas fa-clipboard-check mr-1"></i>
                        {{ __('Plan Kick-off') }}
                    </a>
                @else
                    @if ($project->kickoff->status === 'planned')
                        <a href="{{ route('projects.kickoffs.schedule', $project) }}" class="btn btn-primary mr-2">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ __('Schedule Call') }}
                        </a>
                    @elseif ($project->kickoff->status === 'scheduled')
                        <a href="{{ route('projects.kickoffs.schedule', $project) }}" class="btn btn-outline-primary mr-2">
                            <i class="fas fa-sync-alt mr-1"></i>
                            {{ __('Reschedule Call') }}
                        </a>
                        <a href="{{ route('projects.kickoffs.complete', $project) }}" class="btn btn-primary mr-2">
                            <i class="fas fa-check mr-1"></i>
                            {{ __('Complete Call') }}
                        </a>
                    @endif
                    <form method="POST" action="{{ route('projects.kickoffs.destroy', $project) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this kick-off?') }}')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ __('Delete') }}</x-danger-button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    @if (! $project->kickoff)
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-3">{{ __('No kick-off has been planned yet.') }}</p>
                <a href="{{ route('projects.kickoffs.plan', $project) }}" class="btn btn-primary">
                    <i class="fas fa-clipboard-check mr-1"></i>
                    {{ __('Plan Kick-off') }}
                </a>
            </div>
        </div>
    @else
        @php($statusClasses = ['planned' => 'secondary', 'scheduled' => 'info', 'completed' => 'success'])
        @php($stakeholderLabels = [
            App\Models\Customer::class => __('Customer'),
            App\Models\Contact::class => __('Contact'),
            App\Models\User::class => __('User'),
        ])

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
                                    <th class="text-muted">{{ __('Planned At') }}</th>
                                    <td>{{ $project->kickoff->planned_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Scheduled At') }}</th>
                                    <td>{{ $project->kickoff->scheduled_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Completed At') }}</th>
                                    <td>{{ $project->kickoff->completed_at?->format('Y-m-d H:i') ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Meeting Mode') }}</th>
                                    <td>
                                        @php($modeLabels = [
                                            'onsite' => __('Onsite'),
                                            'virtual_meet' => __('Virtual - Meet'),
                                            'virtual_teams' => __('Virtual - Teams'),
                                        ])
                                        {{ $modeLabels[$project->kickoff->meeting_mode] ?? $project->kickoff->meeting_mode ?? '-' }}
                                        @if ($project->kickoff->meeting_mode === 'onsite' && $project->kickoff->site_location)
                                            <div class="text-muted small">{{ $project->kickoff->site_location }}</div>
                                        @endif
                                        @if (in_array($project->kickoff->meeting_mode, ['virtual_meet', 'virtual_teams'], true) && $project->kickoff->meeting_link)
                                            <div>
                                                <a href="{{ $project->kickoff->meeting_link }}" target="_blank" rel="noopener">
                                                    {{ __('Join link') }}
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ __('Stakeholders') }}</th>
                                    <td>
                                        @forelse ($project->kickoff->stakeholderLinks as $link)
                                            <span class="badge badge-light mr-1">
                                                &#64;{{ $link->stakeholder?->name ?? __('Unknown') }}
                                                <span class="text-muted">({{ $stakeholderLabels[$link->stakeholder_type] ?? __('Stakeholder') }})</span>
                                            </span>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
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
                        <h3 class="card-title">{{ __('Products') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted">{{ __('Assigned Products') }}</div>
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

                @can('project_requirements.create')
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Transcript to Requirements') }}</h3>
                        </div>
                        <div class="card-body">
                            @if ($project->kickoff->status !== 'completed')
                                <p class="text-muted mb-0">
                                    {{ __('Complete the kick-off to import requirements from the transcript.') }}
                                </p>
                            @else
                                <p class="text-muted small">
                                    {{ __('Upload a .txt transcript to generate requirement drafts, then approve before import.') }}
                                </p>

                                @if ($project->kickoff->transcript_path)
                                    <div class="text-muted small mb-2">
                                        {{ __('Last transcript') }}: {{ basename($project->kickoff->transcript_path) }}
                                        @if ($project->kickoff->transcript_uploaded_at)
                                            <span class="ml-2">{{ $project->kickoff->transcript_uploaded_at->format('Y-m-d H:i') }}</span>
                                        @endif
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('projects.requirements.import.preview', $project) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="source" value="kickoff">

                                    <div class="form-group">
                                        <x-input-label for="kickoff_transcript" :value="__('Transcript (.txt)')" />
                                        <input id="kickoff_transcript" name="transcript" type="file" class="form-control-file" accept=".txt,text/plain" required>
                                        <x-input-error class="mt-2" :messages="$errors->get('transcript')" />
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <x-primary-button class="mr-2">{{ __('Analyze & Preview') }}</x-primary-button>
                                        <a href="{{ route('projects.requirements.index', $project) }}" class="btn btn-outline-secondary">
                                            {{ __('View Requirements') }}
                                        </a>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    @endif

    <a href="{{ route('projects.show', $project) }}" class="btn btn-link">
        {{ __('Back to Project') }}
    </a>
</x-app-layout>

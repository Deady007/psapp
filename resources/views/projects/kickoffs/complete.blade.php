<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-lg-8">
                <h1 class="m-0">{{ __('Complete Kick-off') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
            </div>
            <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="{{ route('projects.kickoffs.show', $project) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Kick-off') }}
                </a>
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <div class="text-muted">{{ __('Scheduled For') }}</div>
                <div class="font-weight-bold">{{ $kickoff->scheduled_at?->format('Y-m-d H:i') ?: '-' }}</div>
                <div class="text-muted small">{{ $kickoff->meeting_mode ?: '-' }}</div>
            </div>

            <form method="POST" action="{{ route('projects.kickoffs.complete.update', $project) }}">
                @csrf
                @method('PUT')

                @include('projects.kickoffs.partials.stakeholders-select', [
                    'stakeholderOptions' => $stakeholderOptions,
                    'selectedStakeholders' => old('stakeholders', $selectedStakeholders),
                ])

                <div class="form-group">
                    <x-input-label for="requirements_summary" :value="__('Requirements Summary')" />
                    <textarea id="requirements_summary" name="requirements_summary" rows="4" class="form-control" required>{{ old('requirements_summary', $kickoff->requirements_summary) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('requirements_summary')" />
                </div>

                <div class="form-group">
                    <x-input-label for="timeline_summary" :value="__('Timeline Summary')" />
                    <textarea id="timeline_summary" name="timeline_summary" rows="3" class="form-control">{{ old('timeline_summary', $kickoff->timeline_summary) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('timeline_summary')" />
                </div>

                <div class="form-group">
                    <x-input-label for="notes" :value="__('Completion Notes')" />
                    <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes', $kickoff->notes) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Submit Summary') }}</x-primary-button>
                    <a href="{{ route('projects.kickoffs.show', $project) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-lg-8">
                @php($isReschedule = $kickoff->status === 'scheduled')
                <h1 class="m-0">{{ $isReschedule ? __('Reschedule Kick-off') : __('Schedule Kick-off') }}</h1>
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
            @if ($isReschedule)
                <div class="alert alert-info">
                    {{ __('Current schedule:') }}
                    <strong>{{ $kickoff->scheduled_at?->format('Y-m-d H:i') ?? '-' }}</strong>
                </div>
            @endif
            <div class="mb-3">
                <div class="text-muted">{{ __('Products') }}</div>
                <div>
                    @forelse ($project->products as $product)
                        <span class="badge badge-info mr-1">{{ $product->name }}</span>
                    @empty
                        <span class="text-muted">{{ __('No products assigned yet.') }}</span>
                    @endforelse
                </div>
            </div>

            <form method="POST" action="{{ route('projects.kickoffs.schedule.update', $project) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <x-input-label for="scheduled_at" :value="__('Kick-off Date & Time')" />
                    <x-text-input id="scheduled_at" name="scheduled_at" type="text" data-datetimepicker="1" :value="old('scheduled_at', $kickoff->scheduled_at?->format('Y-m-d H:i'))" required />
                    <x-input-error class="mt-2" :messages="$errors->get('scheduled_at')" />
                </div>

                <div class="form-group">
                    <x-input-label for="meeting_mode" :value="__('Meeting Mode')" />
                    <select id="meeting_mode" name="meeting_mode" class="form-control" required>
                        @php($selectedMode = old('meeting_mode', $kickoff->meeting_mode))
                        <option value="onsite" @selected($selectedMode === 'onsite')>{{ __('Onsite') }}</option>
                        <option value="virtual_meet" @selected($selectedMode === 'virtual_meet')>{{ __('Virtual - Meet') }}</option>
                        <option value="virtual_teams" @selected($selectedMode === 'virtual_teams')>{{ __('Virtual - Teams') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('meeting_mode')" />
                </div>

                <div class="form-group" data-meeting-field="onsite">
                    <x-input-label for="site_location" :value="__('Site Location')" />
                    <x-text-input id="site_location" name="site_location" type="text" :value="old('site_location', $kickoff->site_location)" />
                    <x-input-error class="mt-2" :messages="$errors->get('site_location')" />
                </div>

                <div class="form-group" data-meeting-field="virtual">
                    <x-input-label for="meeting_link" :value="__('Meeting Link')" />
                    <x-text-input id="meeting_link" name="meeting_link" type="url" :value="old('meeting_link', $kickoff->meeting_link)" />
                    <x-input-error class="mt-2" :messages="$errors->get('meeting_link')" />
                </div>

                @include('projects.kickoffs.partials.stakeholders-select', [
                    'stakeholderOptions' => $stakeholderOptions,
                    'selectedStakeholders' => old('stakeholders', $selectedStakeholders),
                ])

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">
                        {{ $isReschedule ? __('Save Reschedule') : __('Save Schedule') }}
                    </x-primary-button>
                    <a href="{{ route('projects.kickoffs.show', $project) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modeSelect = document.getElementById('meeting_mode');
                const siteField = document.querySelector('[data-meeting-field="onsite"]');
                const virtualField = document.querySelector('[data-meeting-field="virtual"]');

                function toggleFields() {
                    const mode = modeSelect.value;
                    if (mode === 'onsite') {
                        siteField.classList.remove('d-none');
                        virtualField.classList.add('d-none');
                    } else {
                        siteField.classList.add('d-none');
                        virtualField.classList.remove('d-none');
                    }
                }

                modeSelect.addEventListener('change', toggleFields);
                toggleFields();
            });
        </script>
    @endpush
</x-app-layout>

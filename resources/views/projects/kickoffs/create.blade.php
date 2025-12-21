<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-lg-8">
                <h1 class="m-0">{{ __('Schedule Kick-off') }}</h1>
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
                <div class="text-muted">{{ __('Products') }}</div>
                <div>
                    @forelse ($project->products as $product)
                        <span class="badge badge-info mr-1">{{ $product->name }}</span>
                    @empty
                        <span class="text-muted">{{ __('No products assigned yet.') }}</span>
                    @endforelse
                </div>
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary btn-sm mt-2">
                    {{ __('Edit Products') }}
                </a>
            </div>

            <form method="POST" action="{{ route('projects.kickoffs.store', $project) }}">
                @csrf

                <div class="form-group">
                    <x-input-label for="purchase_order_number" :value="__('Purchase Order Number')" />
                    <x-text-input id="purchase_order_number" name="purchase_order_number" type="text" :value="old('purchase_order_number')" />
                    <x-input-error class="mt-2" :messages="$errors->get('purchase_order_number')" />
                </div>

                <div class="form-group">
                    <x-input-label for="scheduled_at" :value="__('Kick-off Date & Time')" />
                    <x-text-input id="scheduled_at" name="scheduled_at" type="text" data-datetimepicker="1" :value="old('scheduled_at')" />
                    <x-input-error class="mt-2" :messages="$errors->get('scheduled_at')" />
                </div>

                <div class="form-group">
                    <x-input-label for="meeting_mode" :value="__('Meeting Mode')" />
                    <x-text-input id="meeting_mode" name="meeting_mode" type="text" :value="old('meeting_mode')" />
                    <x-input-error class="mt-2" :messages="$errors->get('meeting_mode')" />
                </div>

                <div class="form-group">
                    <x-input-label for="stakeholders" :value="__('Stakeholders')" />
                    <textarea id="stakeholders" name="stakeholders" rows="3" class="form-control">{{ old('stakeholders') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('stakeholders')" />
                </div>

                <div class="form-group">
                    <x-input-label for="requirements_summary" :value="__('Requirements Summary')" />
                    <textarea id="requirements_summary" name="requirements_summary" rows="3" class="form-control">{{ old('requirements_summary') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('requirements_summary')" />
                </div>

                <div class="form-group">
                    <x-input-label for="timeline_summary" :value="__('Timeline Summary')" />
                    <textarea id="timeline_summary" name="timeline_summary" rows="3" class="form-control">{{ old('timeline_summary') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('timeline_summary')" />
                </div>

                <div class="form-group">
                    <x-input-label for="notes" :value="__('Notes')" />
                    <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                </div>

                <div class="form-group">
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" data-enhance="choices" class="form-control" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', 'draft') === $status)>
                                {{ __($status) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Save') }}</x-primary-button>
                    <a href="{{ route('projects.kickoffs.show', $project) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

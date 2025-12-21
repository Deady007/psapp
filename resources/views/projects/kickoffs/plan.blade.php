<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-lg-8">
                <h1 class="m-0">{{ __('Plan Kick-off') }}</h1>
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

            <form method="POST" action="{{ route('projects.kickoffs.plan.store', $project) }}">
                @csrf

                <div class="form-group">
                    <x-input-label for="purchase_order_number" :value="__('Purchase Order Number')" />
                    <x-text-input id="purchase_order_number" name="purchase_order_number" type="text" :value="old('purchase_order_number')" />
                    <x-input-error class="mt-2" :messages="$errors->get('purchase_order_number')" />
                </div>

                @include('projects.kickoffs.partials.stakeholders-select', [
                    'stakeholderOptions' => $stakeholderOptions,
                    'selectedStakeholders' => old('stakeholders', $selectedStakeholders),
                ])

                <div class="form-group">
                    <x-input-label for="notes" :value="__('Planning Notes')" />
                    <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Save Plan') }}</x-primary-button>
                    <a href="{{ route('projects.kickoffs.show', $project) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

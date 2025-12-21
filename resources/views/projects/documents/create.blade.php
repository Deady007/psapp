<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Upload Document') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('projects.documents.index', $project) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Documents') }}
                </a>
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projects.documents.store', $project) }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <x-input-label for="category" :value="__('Category')" />
                    <x-text-input id="category" name="category" type="text" :value="old('category')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('category')" />
                </div>

                <div class="form-group">
                    <x-input-label for="file" :value="__('Document File')" />
                    <input id="file" name="file" type="file" class="form-control" required>
                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                </div>

                <div class="form-group">
                    <x-input-label for="collected_at" :value="__('Collected At')" />
                    <x-text-input id="collected_at" name="collected_at" type="text" data-datepicker="1" :value="old('collected_at')" />
                    <x-input-error class="mt-2" :messages="$errors->get('collected_at')" />
                </div>

                <div class="form-group">
                    <x-input-label for="notes" :value="__('Notes')" />
                    <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Upload') }}</x-primary-button>
                    <a href="{{ route('projects.documents.index', $project) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

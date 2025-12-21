<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('New Requirement') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('projects.requirements.index', $project) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Requirements') }}
                </a>
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projects.requirements.store', $project) }}">
                @csrf

                <div class="form-group">
                    <x-input-label for="module_name" :value="__('Module Name')" />
                    <x-text-input id="module_name" name="module_name" type="text" :value="old('module_name')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('module_name')" />
                </div>

                <div class="form-group">
                    <x-input-label for="page_name" :value="__('Page Name')" />
                    <x-text-input id="page_name" name="page_name" type="text" :value="old('page_name')" />
                    <x-input-error class="mt-2" :messages="$errors->get('page_name')" />
                </div>

                <div class="form-group">
                    <x-input-label for="title" :value="__('Title')" />
                    <x-text-input id="title" name="title" type="text" :value="old('title')" required />
                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                </div>

                <div class="form-group">
                    <x-input-label for="details" :value="__('Details')" />
                    <textarea id="details" name="details" rows="4" class="form-control">{{ old('details') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('details')" />
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <x-input-label for="priority" :value="__('Priority')" />
                        <select id="priority" name="priority" data-enhance="choices" class="form-control" required>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority }}" @selected(old('priority', 'medium') === $priority)>
                                    {{ __($priority) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('priority')" />
                    </div>

                    <div class="form-group col-md-6">
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" data-enhance="choices" class="form-control" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', 'todo') === $status)>
                                    {{ __($status) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Save') }}</x-primary-button>
                    <a href="{{ route('projects.requirements.index', $project) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

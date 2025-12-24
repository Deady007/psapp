<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Projects') }}</h1>
                <div class="text-muted">{{ __('Monitor delivery, milestones, and the flow of work.') }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    {{ __('New Project') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Filters') }}</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('projects.index') }}">
                <div class="form-row">
                    <div class="form-group col-lg-4">
                        <x-input-label for="customer_id" :value="__('Customer')" />
                        <select id="customer_id" name="customer_id" data-enhance="choices" class="form-control">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(($filters['customer_id'] ?? null) === $customer->id)>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-4">
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" data-enhance="choices" class="form-control">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>
                                    {{ __($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-4 d-flex align-items-end">
                        <x-primary-button class="mr-2">{{ __('Filter') }}</x-primary-button>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Project list') }}</h3>
            <div class="card-tools text-muted">
                {{ __('Showing') }} {{ $projects->firstItem() ?? 0 }}-{{ $projects->lastItem() ?? 0 }} {{ __('of') }} {{ $projects->total() }}
            </div>
        </div>

        @if ($projects->count() === 0)
            <div class="card-body">
                <p class="text-muted mb-0">{{ __('No projects found.') }}</p>
            </div>
        @else
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Start') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}" class="font-weight-bold">
                                        {{ $project->name }}
                                    </a>
                                    @if ($project->code)
                                        <div class="text-muted small">{{ $project->code }}</div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('customers.show', $project->customer) }}">
                                        {{ $project->customer->name }}
                                    </a>
                                </td>
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
                                <td>{{ $project->start_date?->toDateString() ?: '-' }}</td>
                                <td>{{ $project->due_date?->toDateString() ?: '-' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this project?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

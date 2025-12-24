<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Customers') }}</h1>
                <div class="text-muted">{{ __('Keep relationships crisp with a refined, at-a-glance view.') }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    {{ __('New Customer') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="callout callout-info">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">{{ __('Insights') }}</h5>
                <p class="mb-0 text-muted">{{ __('Prioritize your most valuable accounts with confidence.') }}</p>
            </div>
            <span class="badge badge-info">
                {{ __('Total') }}: {{ $customers->total() }}
            </span>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Customer list') }}</h3>
            <div class="card-tools text-muted">
                {{ __('Showing') }} {{ $customers->firstItem() ?? 0 }}-{{ $customers->lastItem() ?? 0 }} {{ __('of') }} {{ $customers->total() }}
            </div>
        </div>

        @if ($customers->count() === 0)
            <div class="card-body">
                <p class="text-muted mb-0">{{ __('No customers found.') }}</p>
            </div>
        @else
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>
                                    <a href="{{ route('customers.show', $customer) }}" class="font-weight-bold">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td>{{ $customer->email ?: '-' }}</td>
                                <td>{{ $customer->phone ?: '-' }}</td>
                                <td>
                                    @if ($customer->status === 'active')
                                        <span class="badge badge-success">{{ __('active') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ __('inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary">
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this customer?') }}')">
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
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

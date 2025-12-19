<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Contacts') }}</h1>
                <div class="text-muted">{{ $customer->name }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('customers.contacts.create', $customer) }}" class="btn btn-primary mr-2">
                    <i class="fas fa-plus mr-1"></i>
                    {{ __('New Contact') }}
                </a>
                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Customer') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Contact list') }}</h3>
            <div class="card-tools text-muted">
                {{ __('Showing') }} {{ $contacts->firstItem() ?? 0 }}-{{ $contacts->lastItem() ?? 0 }} {{ __('of') }} {{ $contacts->total() }}
            </div>
        </div>

        @if ($contacts->count() === 0)
            <div class="card-body">
                <p class="text-muted mb-0">{{ __('No contacts found.') }}</p>
            </div>
        @else
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Designation') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr>
                                <td>
                                    <a href="{{ route('customers.contacts.show', [$customer, $contact]) }}" class="font-weight-bold">
                                        {{ $contact->name }}
                                    </a>
                                </td>
                                <td>{{ $contact->email ?: '-' }}</td>
                                <td>{{ $contact->phone ?: '-' }}</td>
                                <td>{{ $contact->designation ?: '-' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('customers.contacts.edit', [$customer, $contact]) }}" class="btn btn-sm btn-outline-secondary">
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('customers.contacts.destroy', [$customer, $contact]) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this contact?') }}')">
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
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

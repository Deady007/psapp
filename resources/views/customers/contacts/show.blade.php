<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-md-7">
                <h1 class="m-0">{{ $contact->name }}</h1>
                <div class="text-muted">
                    <a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a>
                </div>
            </div>
            <div class="col-md-5 text-md-right mt-3 mt-md-0">
                <a href="{{ route('customers.contacts.edit', [$customer, $contact]) }}" class="btn btn-outline-secondary mr-2">
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('customers.contacts.destroy', [$customer, $contact]) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this contact?') }}')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>{{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tbody>
                    <tr>
                        <th class="text-muted">{{ __('Email') }}</th>
                        <td>{{ $contact->email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('Phone') }}</th>
                        <td>{{ $contact->phone ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('Designation') }}</th>
                        <td>{{ $contact->designation ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('customers.contacts.index', $customer) }}" class="btn btn-link">
                {{ __('Back to Contacts') }}
            </a>
        </div>
    </div>
</x-app-layout>

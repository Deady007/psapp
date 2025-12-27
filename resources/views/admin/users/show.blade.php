<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-md-7">
                <h1 class="m-0">{{ $user->name }}</h1>
            </div>
            <div class="col-md-5 text-md-right mt-3 mt-md-0">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary mr-2">
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" data-confirm="{{ __('Delete this user?') }}" data-confirm-button="{{ __('Yes, delete it') }}" data-cancel-button="{{ __('Cancel') }}">
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
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">{{ __('Role') }}</th>
                        <td>{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.users.index') }}" class="btn btn-link">
                {{ __('Back to Users') }}
            </a>
        </div>
    </div>
</x-app-layout>

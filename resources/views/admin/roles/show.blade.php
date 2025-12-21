<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-md-7">
                <h1 class="m-0">{{ $role->name }}</h1>
            </div>
            <div class="col-md-5 text-md-right mt-3 mt-md-0">
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline-secondary mr-2">
                    {{ __('Edit') }}
                </a>
                @if (! in_array($role->name, ['admin', 'user'], true))
                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this role?') }}')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ __('Delete') }}</x-danger-button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Permissions') }}</h3>
        </div>
        <div class="card-body">
            @if ($role->permissions->isEmpty())
                <p class="text-muted mb-0">-</p>
            @else
                @foreach ($role->permissions as $permission)
                    <span class="badge badge-light">{{ $permission->name }}</span>
                @endforeach
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-link">
                {{ __('Back to Roles') }}
            </a>
        </div>
    </div>
</x-app-layout>

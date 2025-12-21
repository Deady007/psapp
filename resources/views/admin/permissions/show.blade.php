<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-md-7">
                <h1 class="m-0">{{ $permission->name }}</h1>
            </div>
            <div class="col-md-5 text-md-right mt-3 mt-md-0">
                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-outline-secondary mr-2">
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this permission?') }}')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>{{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <p class="mb-0 text-muted">{{ __('Permission details are shown above.') }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-link">
                {{ __('Back to Permissions') }}
            </a>
        </div>
    </div>
</x-app-layout>

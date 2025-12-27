<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Permissions') }}</h1>
                <div class="text-muted">{{ __('Review and tune granular access rules.') }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    {{ __('New Permission') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Permission list') }}</h3>
            <div class="card-tools text-muted">
                {{ __('Showing') }} {{ $permissions->firstItem() ?? 0 }}-{{ $permissions->lastItem() ?? 0 }} {{ __('of') }} {{ $permissions->total() }}
            </div>
        </div>

        @if ($permissions->isEmpty())
            <div class="card-body">
                <p class="text-muted mb-0">{{ __('No permissions found.') }}</p>
            </div>
        @else
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap" data-table="datatable">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.permissions.show', $permission) }}" class="font-weight-bold">
                                        {{ $permission->name }}
                                    </a>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-secondary">
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" class="d-inline" data-confirm="{{ __('Delete this permission?') }}" data-confirm-button="{{ __('Yes, delete it') }}" data-cancel-button="{{ __('Cancel') }}">
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
                {{ $permissions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

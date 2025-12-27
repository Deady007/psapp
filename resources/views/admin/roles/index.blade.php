<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Roles') }}</h1>
                <div class="text-muted">{{ __('Shape responsibilities with curated role profiles.') }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    {{ __('New Role') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Role list') }}</h3>
            <div class="card-tools text-muted">
                {{ __('Showing') }} {{ $roles->firstItem() ?? 0 }}-{{ $roles->lastItem() ?? 0 }} {{ __('of') }} {{ $roles->total() }}
            </div>
        </div>

        @if ($roles->isEmpty())
            <div class="card-body">
                <p class="text-muted mb-0">{{ __('No roles found.') }}</p>
            </div>
        @else
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap" data-table="datatable">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Permissions') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.roles.show', $role) }}" class="font-weight-bold">
                                        {{ $role->name }}
                                    </a>
                                </td>
                                <td>
                                    @if ($role->permissions->isEmpty())
                                        <span class="text-muted">-</span>
                                    @else
                                        @foreach ($role->permissions as $perm)
                                            <span class="badge badge-light">{{ $perm->name }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">
                                        {{ __('Edit') }}
                                    </a>
                                    @if (! in_array($role->name, ['admin', 'user'], true))
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline" data-confirm="{{ __('Delete this role?') }}" data-confirm-button="{{ __('Yes, delete it') }}" data-cancel-button="{{ __('Cancel') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

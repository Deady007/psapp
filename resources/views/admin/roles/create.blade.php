<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('New Role') }}</h1>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                    {{ __('Back to Roles') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <div class="form-group">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="form-group">
                    <x-input-label :value="__('Permissions')" />
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('Module') }}</th>
                                    <th class="text-center">{{ __('View') }}</th>
                                    <th class="text-center">{{ __('Create') }}</th>
                                    <th class="text-center">{{ __('Edit') }}</th>
                                    <th class="text-center">{{ __('Delete') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissionsMatrix as $module => $data)
                                    <tr>
                                        <td class="font-weight-bold">{{ $data['label'] }}</td>
                                        @foreach (['view', 'create', 'edit', 'delete'] as $action)
                                            @php $permName = "{$module}.{$action}"; @endphp
                                            <td class="text-center">
                                                @if (in_array($action, $data['actions'], true))
                                                    <input type="checkbox" name="permissions[]" value="{{ $permName }}" />
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('permissions')" />
                </div>

                <div class="d-flex justify-content-end">
                    <x-primary-button class="mr-2">{{ __('Save') }}</x-primary-button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

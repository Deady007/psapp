<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Role') }}
            </h2>

            <a href="{{ route('admin.roles.show', $role) }}" class="text-sm text-gray-700 hover:text-gray-900">
                {{ __('Back to Role') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $role->name)" required autofocus @disabled($role->name === 'admin') />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="space-y-3">
                            <x-input-label :value="__('Permissions')" />
                            <div class="overflow-x-auto border border-gray-200 rounded-md">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Module') }}</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('View') }}</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Create') }}</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Edit') }}</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Delete') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php $selected = old('permissions', $role->permissions->pluck('name')->toArray()); @endphp
                                        @foreach ($permissionsMatrix as $module => $data)
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $data['label'] }}</td>
                                                @foreach (['view', 'create', 'edit', 'delete'] as $action)
                                                    @php $permName = "{$module}.{$action}"; @endphp
                                                    <td class="px-4 py-3 text-center">
                                                        @if (in_array($action, $data['actions'], true))
                                                            <input
                                                                type="checkbox"
                                                                name="permissions[]"
                                                                value="{{ $permName }}"
                                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                                @checked(in_array($permName, $selected))
                                                            />
                                                        @else
                                                            <span class="text-gray-400">—</span>
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

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update') }}</x-primary-button>
                            <a href="{{ route('admin.roles.show', $role) }}" class="text-sm text-gray-700 hover:text-gray-900">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

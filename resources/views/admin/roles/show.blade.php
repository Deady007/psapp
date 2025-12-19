<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $role->name }}
            </h2>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.roles.edit', $role) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>

                @if (! in_array($role->name, ['admin', 'user'], true))
                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('{{ __('Delete this role?') }}')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ __('Delete') }}</x-danger-button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Permissions') }}</h3>
                        @if ($role->permissions->isEmpty())
                            <p class="mt-2 text-sm text-gray-600">—</p>
                        @else
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($role->permissions as $permission)
                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700 border border-gray-200">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <a href="{{ route('admin.roles.index') }}" class="text-sm text-gray-700 hover:text-gray-900">
                            {{ __('Back to Roles') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

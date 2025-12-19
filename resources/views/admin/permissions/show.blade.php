<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $permission->name }}
            </h2>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('admin.permissions.edit', $permission) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                {{ __('Edit') }}
            </a>

            <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" onsubmit="return confirm('{{ __('Delete this permission?') }}')">
                @csrf
                @method('DELETE')
                <x-danger-button>{{ __('Delete') }}</x-danger-button>
            </form>
        </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Permission Name') }}</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $permission->name }}</p>
                    </div>

                    <div>
                        <a href="{{ route('admin.permissions.index') }}" class="text-sm text-gray-700 hover:text-gray-900">
                            {{ __('Back to Permissions') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

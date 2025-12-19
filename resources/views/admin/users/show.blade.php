<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $user->name }}
            </h2>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('admin.users.edit', $user) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>

                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('Delete this user?') }}')">
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
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Email') }}</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Role') }}</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</p>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-700 hover:text-gray-900">
                            {{ __('Back to Users') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

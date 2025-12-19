<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $customer->name }}
            </h2>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('customers.contacts.index', $customer) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    {{ __('Contacts') }} ({{ $customer->contacts_count }})
                </a>

                <a
                    href="{{ route('projects.index', ['customer_id' => $customer->id]) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    {{ __('Projects') }} ({{ $customer->projects_count }})
                </a>

                <a
                    href="{{ route('customers.edit', $customer) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>

                <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('{{ __('Delete this customer?') }}')">
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
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Status') }}</h3>
                        <div class="mt-1">
                            @if ($customer->status === 'active')
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    {{ __('active') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">
                                    {{ __('inactive') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500">{{ __('Notes') }}</h3>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                            {{ $customer->notes ?: __('—') }}
                        </p>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('customers.index') }}" class="text-sm text-gray-700 hover:text-gray-900">
                            {{ __('Back to Customers') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

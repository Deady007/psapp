<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Contacts') }} — {{ $customer->name }}
            </h2>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('customers.contacts.create', $customer) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    {{ __('New Contact') }}
                </a>

                <a
                    href="{{ route('customers.show', $customer) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    {{ __('Back to Customer') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($contacts->count() === 0)
                        <p class="text-sm text-gray-600">{{ __('No contacts found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Name') }}
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Email') }}
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Phone') }}
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Designation') }}
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($contacts as $contact)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <a href="{{ route('customers.contacts.show', [$customer, $contact]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $contact->name }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                {{ $contact->email ?: '—' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                {{ $contact->phone ?: '—' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                {{ $contact->designation ?: '—' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                                <a href="{{ route('customers.contacts.edit', [$customer, $contact]) }}" class="text-gray-700 hover:text-gray-900">
                                                    {{ __('Edit') }}
                                                </a>

                                                <form method="POST" action="{{ route('customers.contacts.destroy', [$customer, $contact]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this contact?') }}')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $contacts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

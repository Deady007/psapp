<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-200/80">{{ __('Contacts') }}</p>
                <h2 class="text-3xl font-semibold leading-tight text-white font-display">
                    {{ __('Contacts') }} - {{ $customer->name }}
                </h2>
                <p class="text-sm text-slate-300">{{ __('Keep the right people close with a clean contact view.') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('customers.contacts.create', $customer) }}" class="soft-cta">
                    {{ __('New Contact') }}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h5.5V3.75a.75.75 0 0 1 1.5 0v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                    </svg>
                </a>

                <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-amber-100 ring-1 ring-white/10 transition hover:-translate-y-0.5 hover:bg-white/20">
                    {{ __('Back to Customer') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="soft-panel overflow-hidden motion-safe:animate-reveal">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-6 py-4">
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Directory') }}</p>
                        <h3 class="text-lg font-semibold text-white font-display">{{ __('Contact list') }}</h3>
                    </div>
                    <p class="text-xs text-slate-400">
                        {{ __('Showing') }} {{ $contacts->firstItem() ?? 0 }}-{{ $contacts->lastItem() ?? 0 }} {{ __('of') }} {{ $contacts->total() }}
                    </p>
                </div>

                @if ($contacts->count() === 0)
                    <div class="px-6 py-8 text-sm text-slate-300">{{ __('No contacts found.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Name') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Email') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Phone') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Designation') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($contacts as $contact)
                                    <tr class="group transition hover:bg-white/5">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('customers.contacts.show', [$customer, $contact]) }}" class="font-semibold text-white transition group-hover:text-amber-200">
                                                {{ $contact->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            {{ $contact->email ?: 'ƒ?"' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            {{ $contact->phone ?: 'ƒ?"' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            {{ $contact->designation ?: 'ƒ?"' }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('customers.contacts.edit', [$customer, $contact]) }}" class="text-slate-200 transition hover:text-white">
                                                    {{ __('Edit') }}
                                                </a>

                                                <form method="POST" action="{{ route('customers.contacts.destroy', [$customer, $contact]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this contact?') }}')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="text-rose-300 transition hover:text-rose-100">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-white/10 px-6 py-4">
                        {{ $contacts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

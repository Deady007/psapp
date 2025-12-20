<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-200/80">{{ __('Customer Atlas') }}</p>
                <h2 class="text-3xl font-semibold leading-tight text-white font-display">
                    {{ __('Customers') }}
                </h2>
                <p class="text-sm text-slate-300">{{ __('Keep relationships crisp with a refined, at-a-glance view.') }}</p>
            </div>

            <a href="{{ route('customers.create') }}" class="soft-cta">
                {{ __('New Customer') }}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h5.5V3.75a.75.75 0 0 1 1.5 0v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="soft-panel p-6 motion-safe:animate-reveal">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Insights') }}</p>
                        <h3 class="text-lg font-semibold text-white font-display">{{ __('Relationship pulse') }}</h3>
                        <p class="text-sm text-slate-300">{{ __('Prioritize your most valuable accounts with confidence.') }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/70">
                        <span class="soft-badge">{{ __('Total') }}: {{ $customers->total() }}</span>
                    </div>
                </div>
            </div>

            <div class="soft-panel overflow-hidden motion-safe:animate-reveal reveal-delay-2">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-6 py-4">
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Overview') }}</p>
                        <h3 class="text-lg font-semibold text-white font-display">{{ __('Customer list') }}</h3>
                    </div>
                    <p class="text-xs text-slate-400">
                        {{ __('Showing') }} {{ $customers->firstItem() ?? 0 }}-{{ $customers->lastItem() ?? 0 }} {{ __('of') }} {{ $customers->total() }}
                    </p>
                </div>

                @if ($customers->count() === 0)
                    <div class="px-6 py-8 text-sm text-slate-300">{{ __('No customers found.') }}</div>
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
                                        {{ __('Status') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($customers as $customer)
                                    <tr class="group transition hover:bg-white/5">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('customers.show', $customer) }}" class="font-semibold text-white transition group-hover:text-amber-200">
                                                {{ $customer->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            {{ $customer->email ?: 'ƒ?"' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            {{ $customer->phone ?: 'ƒ?"' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($customer->status === 'active')
                                                <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-200 ring-1 ring-emerald-400/30">
                                                    {{ __('active') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-200 ring-1 ring-amber-400/30">
                                                    {{ __('inactive') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('customers.edit', $customer) }}" class="text-slate-200 transition hover:text-white">
                                                    {{ __('Edit') }}
                                                </a>

                                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('{{ __('Delete this customer?') }}')">
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
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

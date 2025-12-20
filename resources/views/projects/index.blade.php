<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-200/80">{{ __('Portfolio') }}</p>
                <h2 class="text-3xl font-semibold leading-tight text-white font-display">
                    {{ __('Projects') }}
                </h2>
                <p class="text-sm text-slate-300">{{ __('Monitor delivery, milestones, and the flow of work.') }}</p>
            </div>

            <a href="{{ route('projects.create') }}" class="soft-cta">
                {{ __('New Project') }}
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
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Filters') }}</p>
                        <h3 class="text-lg font-semibold text-white font-display">{{ __('Refine the pipeline') }}</h3>
                        <p class="text-sm text-slate-300">{{ __('Focus the list by customer and status for faster decisions.') }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/70">
                        <span class="soft-badge">{{ __('Total') }}: {{ $projects->total() }}</span>
                    </div>
                </div>

                <form method="GET" action="{{ route('projects.index') }}" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <x-input-label for="customer_id" :value="__('Customer')" class="text-slate-200" />
                        <select
                            id="customer_id"
                            name="customer_id"
                            data-enhance="choices"
                            class="w-full rounded-2xl border-white/10 bg-white/5 text-slate-100 shadow-sm focus:border-amber-400 focus:ring-amber-400"
                        >
                            <option value="">{{ __('All') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(($filters['customer_id'] ?? null) === $customer->id)>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <x-input-label for="status" :value="__('Status')" class="text-slate-200" />
                        <select
                            id="status"
                            name="status"
                            data-enhance="choices"
                            class="w-full rounded-2xl border-white/10 bg-white/5 text-slate-100 shadow-sm focus:border-amber-400 focus:ring-amber-400"
                        >
                            <option value="">{{ __('All') }}</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>
                                    {{ __($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button class="min-w-0 px-6">{{ __('Filter') }}</x-primary-button>
                        <a href="{{ route('projects.index') }}" class="text-sm font-semibold text-amber-100/80 hover:text-amber-50">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>

            <div class="soft-panel overflow-hidden motion-safe:animate-reveal reveal-delay-2">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-6 py-4">
                    <div class="flex flex-col gap-2">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Overview') }}</p>
                        <h3 class="text-lg font-semibold text-white font-display">{{ __('Project list') }}</h3>
                    </div>
                    <p class="text-xs text-slate-400">
                        {{ __('Showing') }} {{ $projects->firstItem() ?? 0 }}-{{ $projects->lastItem() ?? 0 }} {{ __('of') }} {{ $projects->total() }}
                    </p>
                </div>

                @if ($projects->count() === 0)
                    <div class="px-6 py-8 text-sm text-slate-300">{{ __('No projects found.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Name') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Customer') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Status') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Start') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Due') }}
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($projects as $project)
                                    <tr class="group transition hover:bg-white/5">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('projects.show', $project) }}" class="font-semibold text-white transition group-hover:text-amber-200">
                                                {{ $project->name }}
                                            </a>
                                            @if ($project->code)
                                                <div class="text-xs text-slate-400">{{ $project->code }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            <a href="{{ route('customers.show', $project->customer) }}" class="font-semibold text-amber-100 transition hover:text-amber-50">
                                                {{ $project->customer->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($project->status === 'active')
                                                <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-200 ring-1 ring-emerald-400/30">
                                                    {{ __('active') }}
                                                </span>
                                            @elseif ($project->status === 'on_hold')
                                                <span class="inline-flex items-center rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-200 ring-1 ring-amber-400/30">
                                                    {{ __('on_hold') }}
                                                </span>
                                            @elseif ($project->status === 'completed')
                                                <span class="inline-flex items-center rounded-full bg-sky-500/15 px-3 py-1 text-xs font-semibold text-sky-200 ring-1 ring-sky-400/30">
                                                    {{ __('completed') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200 ring-1 ring-white/15">
                                                    {{ __('draft') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            {{ $project->start_date?->toDateString() ?: 'ƒ?"' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            {{ $project->due_date?->toDateString() ?: 'ƒ?"' }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('projects.edit', $project) }}" class="text-slate-200 transition hover:text-white">
                                                    {{ __('Edit') }}
                                                </a>

                                                <form method="POST" action="{{ route('projects.destroy', $project) }}" class="inline" onsubmit="return confirm('{{ __('Delete this project?') }}')">
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
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-amber-200/80">{{ __('Project Overview') }}</p>
                <h2 class="text-3xl font-semibold leading-tight text-white font-display">
                    {{ $project->name }}
                </h2>
                <p class="text-sm text-slate-300">{{ __('Keep delivery on track with a refined project snapshot.') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('projects.edit', $project) }}" class="soft-cta">
                    {{ __('Edit') }}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path d="M5.433 13.917 4.25 15.1v-2.29l7.945-7.945 2.29 2.289-7.052 7.052ZM13.605 1.75l2.645 2.645a.75.75 0 0 1 0 1.06l-1.48 1.48-3.705-3.705 1.48-1.48a.75.75 0 0 1 1.06 0Z" />
                    </svg>
                </a>

                <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('{{ __('Delete this project?') }}')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button class="min-w-0 px-6">{{ __('Delete') }}</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="soft-panel p-6 lg:col-span-2 motion-safe:animate-reveal">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Customer') }}</p>
                                <a href="{{ route('customers.show', $project->customer) }}" class="text-lg font-semibold text-white transition hover:text-amber-200">
                                    {{ $project->customer->name }}
                                </a>
                            </div>
                            <div>
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
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Code') }}</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ $project->code ?: 'ƒ?"' }}</p>
                            </div>
                            <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Status') }}</p>
                                <p class="mt-2 text-sm font-semibold text-white">{{ __($project->status) }}</p>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Description') }}</p>
                            <p class="mt-2 text-sm text-slate-300 whitespace-pre-line">{{ $project->description ?: __('ƒ?"') }}</p>
                        </div>
                    </div>
                </div>

                <div class="soft-card p-6 motion-safe:animate-reveal reveal-delay-2">
                    <div class="flex flex-col gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Timeline') }}</p>
                            <h3 class="text-lg font-semibold text-white font-display">{{ __('Dates') }}</h3>
                        </div>
                        <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Start Date') }}</p>
                            <p class="mt-2 text-sm font-semibold text-white">{{ $project->start_date?->toDateString() ?: 'ƒ?"' }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Due Date') }}</p>
                            <p class="mt-2 text-sm font-semibold text-white">{{ $project->due_date?->toDateString() ?: 'ƒ?"' }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/70">{{ __('Next Move') }}</p>
                            <p class="mt-2 text-sm text-slate-300">{{ __('Share progress, unblock tasks, and keep stakeholders aligned.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ route('projects.index') }}" class="text-sm font-semibold text-amber-100/80 transition hover:text-amber-50">
                    {{ __('Back to Projects') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

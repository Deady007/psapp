@push('css')
    @vite(['resources/css/app.css'])
@endpush

@push('js')
    @vite(['resources/js/app.js'])
@endpush
<x-app-layout bodyClass="kanban-admin">
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300/70">{{ __('Project') }}</p>
                <h1 class="text-2xl font-semibold text-emerald-100">{{ $project->name }}</h1>
                <p class="text-sm text-emerald-200/80">{{ __('Kanban modules for development and testing flow.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('projects.show', $project) }}" class="kanban-button kanban-button-ghost">
                    {{ __('Back to Project') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="flex flex-col gap-6">
        @include('projects.partials.modules-nav', ['project' => $project])

        <div class="soft-card p-3">
            <div class="flex flex-wrap gap-2">
                <span class="kanban-chip kanban-chip-active">{{ __('Kanban') }}</span>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($boards as $board)
                <div class="soft-card p-6">
                    <div class="flex h-full flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <span class="soft-badge">{{ $board->type }}</span>
                            <span class="text-xs text-emerald-300/70">
                                {{ __('Updated') }} {{ $board->updated_at?->toDateString() }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <h2 class="text-xl font-semibold text-emerald-100">{{ $board->name }}</h2>
                            <p class="text-sm text-emerald-200/70">
                                {{ $board->isDevelopment() ? __('Track user stories and delivery flow.') : __('Validate completed stories and log outcomes.') }}
                            </p>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('projects.kanban.boards.show', [$project, $board]) }}" class="kanban-button kanban-button-primary">
                                {{ __('Open Board') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

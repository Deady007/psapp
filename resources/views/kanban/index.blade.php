@php
    $developmentBoard = $project->developmentBoard;
    $testingBoard = $project->testingBoard;
    $currentBoard = request()->route('board');
    $isDevelopmentBoard = $currentBoard instanceof \App\Models\ProjectBoard && $currentBoard->isDevelopment();
    $isTestingBoard = $currentBoard instanceof \App\Models\ProjectBoard && $currentBoard->isTesting();
    $tabClass = fn (bool $active) => $active ? 'kanban-chip kanban-chip-active' : 'kanban-chip';
@endphp

<x-kanban-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6">
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

            <div class="flex flex-wrap gap-2">
                <a class="{{ $tabClass(request()->routeIs('projects.show')) }}" href="{{ route('projects.show', $project) }}">
                    {{ __('Overview') }}
                </a>
                <a class="{{ $tabClass(request()->routeIs('projects.kickoffs.*')) }}" href="{{ route('projects.kickoffs.show', $project) }}">
                    {{ __('Kick-off') }}
                </a>
                <a class="{{ $tabClass(request()->routeIs('projects.requirements.*')) }}" href="{{ route('projects.requirements.index', $project) }}">
                    {{ __('Requirements') }}
                </a>
                <a class="{{ $tabClass(request()->routeIs('projects.drive-documents.*')) }}" href="{{ route('projects.drive-documents.index', $project) }}">
                    {{ __('Drive Documents') }}
                </a>
                <a class="{{ $tabClass($isDevelopmentBoard) }}" href="{{ $developmentBoard ? route('projects.kanban.boards.show', [$project, $developmentBoard]) : route('projects.kanban.index', $project) }}">
                    {{ __('Development') }}
                </a>
                <a class="{{ $tabClass($isTestingBoard) }}" href="{{ $testingBoard ? route('projects.kanban.boards.show', [$project, $testingBoard]) : route('projects.kanban.index', $project) }}">
                    {{ __('Testing') }}
                </a>
            </div>
        </div>
    </x-slot>

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
</x-kanban-layout>

@php
    use App\Models\BoardDocument;
    use App\Models\Bug;
    use App\Models\ProjectBoard;
    use App\Models\Story;
    use App\Models\TestingCard;
    use Illuminate\Support\Str;

    $developmentBoard = $project->developmentBoard;
    $testingBoard = $project->testingBoard;
    $isDevelopmentBoard = $board->isDevelopment();
    $isTestingBoard = $board->isTesting();
    $tabClass = fn (bool $active) => $active ? 'kanban-chip kanban-chip-active' : 'kanban-chip';
    $toggleClass = fn (bool $active) => $active ? 'kanban-button kanban-button-primary' : 'kanban-button';
    $statusOptions = $isDevelopmentBoard ? ProjectBoard::DEVELOPMENT_COLUMNS : ProjectBoard::TESTING_COLUMNS;
    $assigneeOptions = $isDevelopmentBoard ? $developers : $testers;
    $columnsById = $columns->keyBy('id');
    $blockerColumnId = $isDevelopmentBoard ? optional($columns->firstWhere('name', ProjectBoard::DEVELOPMENT_COLUMNS[4]))->id : null;
    $reviewColumnId = $isDevelopmentBoard ? optional($columns->firstWhere('name', ProjectBoard::DEVELOPMENT_COLUMNS[3]))->id : null;
    $filterBase = array_filter([
        'search' => $filters['search'] ?: null,
        'assignee' => $filters['assignee'] ?: null,
        'priority' => $filters['priority'] ?: null,
        'status' => $filters['status'] ?: null,
        'due' => $filters['due'] ?: null,
        'has_bugs' => $filters['has_bugs'] ? '1' : null,
        'overdue' => $filters['overdue'] ? '1' : null,
        'my_items' => $filters['my_items'] ? '1' : null,
        'ready' => $filters['ready'] ?: null,
        'view' => $viewMode,
    ], fn ($value) => $value !== null && $value !== '');
    $filterUrl = function (array $overrides) use ($filterBase, $project, $board) {
        $query = array_merge($filterBase, $overrides);
        $query = array_filter($query, fn ($value) => $value !== null && $value !== '');
        $base = route('projects.kanban.boards.show', [$project, $board]);

        return $query === [] ? $base : $base.'?'.http_build_query($query);
    };
@endphp

<x-kanban-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col gap-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300/70">{{ __('Project') }}</p>
                    <h1 class="text-2xl font-semibold text-emerald-100">{{ $project->name }}</h1>
                    <p class="text-sm text-emerald-200/80">
                        {{ $board->type }} {{ __('board for development and testing flow.') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('projects.show', $project) }}" class="kanban-button kanban-button-ghost">
                        {{ __('Back to Project') }}
                    </a>
                    <a href="{{ route('projects.kanban.index', $project) }}" class="kanban-button kanban-button-ghost">
                        {{ __('All Boards') }}
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

            <div class="flex flex-wrap gap-2">
                <span class="kanban-chip kanban-chip-active">{{ __('Kanban') }}</span>
            </div>
        </div>
    </x-slot>

    <div
        x-data="{
            drawerOpen: false,
            drawerItem: null,
            moveColumnId: '',
            blockerReason: '',
            testingMoveColumnId: '',
            testingResult: '',
            showCreateStory: false,
            showCreateBug: false,
            blockerColumnId: @js($blockerColumnId),
            blockerReasons: @js(Story::BLOCKER_REASON_LABELS),
            openDrawer(item) {
                this.drawerItem = item;
                this.drawerOpen = true;
                if (item.type === 'story') {
                    this.moveColumnId = item.project_board_column_id || '';
                    this.blockerReason = item.blocker_reason || '';
                }
                if (item.type === 'testing') {
                    this.testingMoveColumnId = item.project_board_column_id || '';
                    this.testingResult = item.result || '';
                }
            },
        }"
        x-on:keydown.escape.window="drawerOpen = false; showCreateStory = false; showCreateBug = false"
        class="flex flex-col gap-6"
    >
        <div class="soft-card p-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap gap-2">
                    @if ($isDevelopmentBoard)
                        <button type="button" class="kanban-button kanban-button-primary" x-on:click="showCreateStory = true">
                            {{ __('Create Story') }}
                        </button>
                        <form method="POST" action="{{ route('projects.kanban.boards.documents.store', [$project, $board]) }}">
                            @csrf
                            <input type="hidden" name="type" value="{{ BoardDocument::TYPE_USER_MANUAL }}">
                            <button type="submit" class="kanban-button">
                                {{ __('Generate User Manual') }}
                            </button>
                        </form>
                    @else
                        <button type="button" class="kanban-button kanban-button-primary" x-on:click="showCreateBug = true">
                            {{ __('Create Bug/Issue') }}
                        </button>
                        <form method="POST" action="{{ route('projects.kanban.boards.documents.store', [$project, $board]) }}">
                            @csrf
                            <input type="hidden" name="type" value="{{ BoardDocument::TYPE_VALIDATION_REPORT }}">
                            <button type="submit" class="kanban-button">
                                {{ __('Generate Validation Report') }}
                            </button>
                        </form>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ $filterUrl(['view' => 'board']) }}" class="{{ $toggleClass($viewMode === 'board') }}">
                        {{ __('Board') }}
                    </a>
                    <a href="{{ $filterUrl(['view' => 'list']) }}" class="{{ $toggleClass($viewMode === 'list') }}">
                        {{ __('List') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="soft-card p-4">
            <form method="GET" action="{{ route('projects.kanban.boards.show', [$project, $board]) }}" class="flex flex-col gap-4">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                @if ($filters['has_bugs'])
                    <input type="hidden" name="has_bugs" value="1">
                @endif
                @if ($filters['overdue'])
                    <input type="hidden" name="overdue" value="1">
                @endif
                @if ($filters['my_items'])
                    <input type="hidden" name="my_items" value="1">
                @endif
                @if ($filters['ready'] !== '')
                    <input type="hidden" name="ready" value="{{ $filters['ready'] }}">
                @endif
                <div class="flex flex-wrap items-center gap-3">
                    <div class="min-w-[12rem] flex-1">
                        <input
                            type="search"
                            name="search"
                            value="{{ $filters['search'] }}"
                            placeholder="{{ $isDevelopmentBoard ? __('Search stories, keys, or notes...') : __('Search testing items...') }}"
                            class="kanban-input"
                        >
                    </div>
                    <div class="min-w-[10rem]">
                        <select name="assignee" class="kanban-select">
                            <option value="">{{ __('Assignee') }}</option>
                            @foreach ($assigneeOptions as $assignee)
                                <option value="{{ $assignee->id }}" @selected((string) $filters['assignee'] === (string) $assignee->id)>
                                    {{ $assignee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[9rem]">
                        <select name="priority" class="kanban-select">
                            <option value="">{{ __('Priority') }}</option>
                            @foreach (Story::PRIORITIES as $priority)
                                <option value="{{ $priority }}" @selected($filters['priority'] === $priority)>
                                    {{ $priority }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[9rem]">
                        <select name="status" class="kanban-select">
                            <option value="">{{ __('Status') }}</option>
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status }}" @selected($filters['status'] === $status)>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[8rem]">
                        <select name="due" class="kanban-select">
                            <option value="">{{ __('Due') }}</option>
                            <option value="today" @selected($filters['due'] === 'today')>{{ __('Today') }}</option>
                            <option value="week" @selected($filters['due'] === 'week')>{{ __('Next 7 days') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="kanban-button kanban-button-primary">{{ __('Apply') }}</button>
                    <a href="{{ route('projects.kanban.boards.show', [$project, $board]) }}" class="kanban-button kanban-button-ghost">{{ __('Reset') }}</a>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ $filterUrl(['has_bugs' => $filters['has_bugs'] ? null : '1']) }}" class="{{ $tabClass($filters['has_bugs']) }}">
                        {{ __('Has Bugs') }}
                    </a>
                    <a href="{{ $filterUrl(['overdue' => $filters['overdue'] ? null : '1']) }}" class="{{ $tabClass($filters['overdue']) }}">
                        {{ __('Overdue') }}
                    </a>
                    <a href="{{ $filterUrl(['my_items' => $filters['my_items'] ? null : '1']) }}" class="{{ $tabClass($filters['my_items']) }}">
                        {{ __('My Items') }}
                    </a>
                    @if ($isDevelopmentBoard)
                        <a href="{{ $filterUrl(['ready' => $filters['ready'] === 'review' ? null : 'review']) }}" class="{{ $tabClass($filters['ready'] === 'review') }}">
                            {{ __('Ready for Review') }}
                        </a>
                    @else
                        <a href="{{ $filterUrl(['ready' => $filters['ready'] === 'testing' ? null : 'testing']) }}" class="{{ $tabClass($filters['ready'] === 'testing') }}">
                            {{ __('Ready for Testing') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
        @if ($viewMode === 'board')
            <div class="flex gap-4 overflow-x-auto pb-4 min-h-[24rem] h-[calc(100vh-28rem)] lg:h-[calc(100vh-24rem)]">
                @foreach ($columns as $column)
                    @php
                        $columnStories = $storiesByColumn->get($column->id, collect());
                        $columnTesting = $testingCardsByColumn->get($column->id, collect());
                        $itemCount = $isDevelopmentBoard ? $columnStories->count() : $columnTesting->count();
                    @endphp
                    <div class="flex min-w-[18rem] flex-col rounded-3xl border border-emerald-400/20 bg-black/70 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-semibold text-emerald-100">{{ $column->name }}</span>
                                <span class="text-xs text-emerald-300/70">{{ $itemCount }} {{ __('items') }}</span>
                            </div>
                            <span class="soft-badge">{{ $itemCount }}</span>
                        </div>
                        <div class="mt-4 flex-1 overflow-y-auto">
                            <div class="flex flex-col gap-3">
                                @if ($isDevelopmentBoard)
                                    @forelse ($columnStories as $story)
                                        @php
                                            $assigneeName = $story->assignee?->name ?? __('Unassigned');
                                            $assigneeInitials = $story->assignee
                                                ? collect(preg_split('/\s+/', trim($assigneeName)) ?: [])
                                                    ->filter()
                                                    ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
                                                    ->take(2)
                                                    ->join('')
                                                : '--';
                                            $columnName = $columnsById->get($story->project_board_column_id)?->name ?? __('Unknown');
                                            $testingCard = $story->testingCard;
                                            $testingStatus = __('Not Sent');
                                            if ($testingCard) {
                                                if ($testingCard->result === TestingCard::RESULT_PASS) {
                                                    $testingStatus = __('Pass');
                                                } elseif ($testingCard->result === TestingCard::RESULT_FAIL) {
                                                    $testingStatus = __('Fail');
                                                } else {
                                                    $testingStatus = $testingCard->column?->name ?? __('In Testing');
                                                }
                                            }
                                            $isOverdue = $story->due_date && $story->due_date->lt(now()->startOfDay());
                                            $storyPayload = [
                                                'type' => 'story',
                                                'id' => $story->id,
                                                'issue_key' => $story->issue_key ?: sprintf('STORY-%s', $story->id),
                                                'title' => $story->title,
                                                'column_name' => $columnName,
                                                'project_board_column_id' => $story->project_board_column_id,
                                                'priority' => $story->priority,
                                                'due_date' => $story->due_date?->toDateString(),
                                                'description' => $story->description ?? '',
                                                'acceptance_criteria' => $story->acceptance_criteria ?? '',
                                                'notes' => $story->notes ?? '',
                                                'labels' => $story->labels ?? [],
                                                'labels_string' => $story->labels ? implode(', ', $story->labels) : '',
                                                'estimate' => $story->estimate,
                                                'estimate_unit' => $story->estimate_unit ?? '',
                                                'reference_links' => $story->reference_links ?? [],
                                                'reference_links_string' => $story->reference_links ? implode(PHP_EOL, $story->reference_links) : '',
                                                'database_changes' => $story->database_changes ?? [],
                                                'page_mappings' => $story->page_mappings ?? [],
                                                'database_changes_confirmed' => (bool) $story->database_changes_confirmed,
                                                'page_mappings_confirmed' => (bool) $story->page_mappings_confirmed,
                                                'blocker_reason' => $story->blocker_reason ?? '',
                                                'assignee_id' => $story->assignee_id,
                                                'assignee_name' => $assigneeName,
                                                'assignee_initials' => $assigneeInitials,
                                                'testing_status' => $testingStatus,
                                                'testing_card' => $testingCard ? [
                                                    'id' => $testingCard->id,
                                                    'result' => $testingCard->result,
                                                    'column' => $testingCard->column?->name,
                                                    'tester_name' => $testingCard->tester?->name,
                                                ] : null,
                                                'bugs' => $story->bugs->map(fn ($bug) => [
                                                    'issue_key' => $bug->issue_key ?: sprintf('BUG-%s', $bug->id),
                                                    'title' => $bug->title,
                                                    'status' => $bug->status,
                                                    'severity' => $bug->severity,
                                                ])->values(),
                                                'tasks' => $story->tasks->map(fn ($task) => [
                                                    'title' => $task->title,
                                                    'is_completed' => $task->is_completed,
                                                ])->values(),
                                                'created_at' => $story->created_at?->toDateTimeString(),
                                                'updated_at' => $story->updated_at?->toDateTimeString(),
                                                'latest_stage_at' => $story->latestStatusHistory?->moved_at?->toDateTimeString(),
                                                'update_url' => route('projects.kanban.boards.stories.update', [$project, $board, $story]),
                                                'assign_url' => route('projects.kanban.boards.stories.assign', [$project, $board, $story]),
                                                'move_url' => route('projects.kanban.boards.stories.move', [$project, $board, $story]),
                                                'send_to_testing_url' => route('projects.kanban.boards.stories.send-to-testing', [$project, $board, $story]),
                                                'can_send_to_testing' => $reviewColumnId !== null && $story->project_board_column_id === $reviewColumnId,
                                            ];
                                        @endphp
                                        <button
                                            type="button"
                                            class="group flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-3 text-left transition hover:border-emerald-300/60 hover:shadow-[0_0_16px_rgba(0,255,117,0.25)]"
                                            x-on:click="openDrawer(@js($storyPayload))"
                                        >
                                            <div class="flex items-start justify-between gap-2">
                                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-emerald-300/80">
                                                    {{ $story->issue_key ?: sprintf('STORY-%s', $story->id) }}
                                                </span>
                                                <span class="kanban-chip">{{ $story->priority }}</span>
                                            </div>
                                            <h3 class="text-sm font-semibold text-emerald-100 line-clamp-2">{{ $story->title }}</h3>
                                            <div class="flex flex-wrap items-center gap-2 text-xs text-emerald-200/80">
                                                <div class="flex items-center gap-2">
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border border-emerald-400/40 bg-black/80 text-[0.6rem] font-semibold text-emerald-100">
                                                        {{ $assigneeInitials }}
                                                    </span>
                                                    <span class="truncate">{{ $assigneeName }}</span>
                                                </div>
                                                <span class="text-emerald-300/60">•</span>
                                                <span>
                                                    {{ __('Due') }}
                                                    {{ $story->due_date ? $story->due_date->format('M d') : __('TBD') }}
                                                </span>
                                                @if ($isOverdue)
                                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-200">{{ __('Overdue') }}</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <span class="kanban-chip">{{ __('Bugs') }} {{ $story->bugs->count() }}</span>
                                                <span class="kanban-chip">{{ __('Testing') }}: {{ $testingStatus }}</span>
                                                @if ($columnName === ProjectBoard::DEVELOPMENT_COLUMNS[4])
                                                    <span class="kanban-chip kanban-chip-active">{{ __('Blocked') }}</span>
                                                @endif
                                            </div>
                                            @if ($story->latestStatusHistory?->moved_at)
                                                <span class="text-[0.65rem] uppercase tracking-[0.2em] text-emerald-300/70">
                                                    {{ __('In stage') }} {{ $story->latestStatusHistory->moved_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </button>
                                    @empty
                                        <div class="rounded-2xl border border-emerald-400/10 bg-black/60 p-3 text-xs text-emerald-300/70">
                                            {{ __('No stories in this column.') }}
                                        </div>
                                    @endforelse
                                @else
                                    @forelse ($columnTesting as $testingCard)
                                        @php
                                            $story = $testingCard->story;
                                            $storyIssue = $story?->issue_key ?: ($story ? sprintf('STORY-%s', $story->id) : __('Story'));
                                            $testerName = $testingCard->tester?->name ?? __('Unassigned');
                                            $testerInitials = $testingCard->tester
                                                ? collect(preg_split('/\s+/', trim($testerName)) ?: [])
                                                    ->filter()
                                                    ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
                                                    ->take(2)
                                                    ->join('')
                                                : '--';
                                            $resultLabel = $testingCard->result === TestingCard::RESULT_PASS
                                                ? __('Pass')
                                                : ($testingCard->result === TestingCard::RESULT_FAIL ? __('Fail') : __('Pending'));
                                            $testingPayload = [
                                                'type' => 'testing',
                                                'id' => $testingCard->id,
                                                'project_board_column_id' => $testingCard->project_board_column_id,
                                                'column_name' => $testingCard->column?->name ?? __('Unknown'),
                                                'result' => $testingCard->result,
                                                'notes' => $testingCard->notes ?? '',
                                                'tester_id' => $testingCard->tester_id,
                                                'tester_name' => $testerName,
                                                'tester_initials' => $testerInitials,
                                                'story_id' => $story?->id,
                                                'story_issue_key' => $storyIssue,
                                                'story_title' => $story?->title,
                                                'story_assignee' => $story?->assignee?->name,
                                                'due_date' => $story?->due_date?->toDateString(),
                                                'bugs' => $testingCard->bugs->map(fn ($bug) => [
                                                    'issue_key' => $bug->issue_key ?: sprintf('BUG-%s', $bug->id),
                                                    'title' => $bug->title,
                                                    'status' => $bug->status,
                                                    'severity' => $bug->severity,
                                                ])->values(),
                                                'created_at' => $testingCard->created_at?->toDateTimeString(),
                                                'updated_at' => $testingCard->updated_at?->toDateTimeString(),
                                                'started_at' => $testingCard->started_at?->toDateTimeString(),
                                                'completed_at' => $testingCard->completed_at?->toDateTimeString(),
                                                'assign_url' => route('projects.kanban.boards.testing-cards.assign', [$project, $board, $testingCard]),
                                                'move_url' => route('projects.kanban.boards.testing-cards.move', [$project, $board, $testingCard]),
                                                'result_url' => route('projects.kanban.boards.testing-cards.result', [$project, $board, $testingCard]),
                                            ];
                                        @endphp
                                        <button
                                            type="button"
                                            class="group flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-3 text-left transition hover:border-emerald-300/60 hover:shadow-[0_0_16px_rgba(0,255,117,0.25)]"
                                            x-on:click="openDrawer(@js($testingPayload))"
                                        >
                                            <div class="flex items-start justify-between gap-2">
                                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-emerald-300/80">
                                                    {{ $storyIssue }}
                                                </span>
                                                <span class="kanban-chip">{{ $resultLabel }}</span>
                                            </div>
                                            <h3 class="text-sm font-semibold text-emerald-100 line-clamp-2">{{ $story?->title ?? __('Testing Item') }}</h3>
                                            <div class="flex flex-wrap items-center gap-2 text-xs text-emerald-200/80">
                                                <div class="flex items-center gap-2">
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border border-emerald-400/40 bg-black/80 text-[0.6rem] font-semibold text-emerald-100">
                                                        {{ $testerInitials }}
                                                    </span>
                                                    <span class="truncate">{{ $testerName }}</span>
                                                </div>
                                                <span class="text-emerald-300/60">•</span>
                                                <span>
                                                    {{ __('Due') }}
                                                    {{ $story?->due_date ? $story->due_date->format('M d') : __('TBD') }}
                                                </span>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <span class="kanban-chip">{{ __('Bugs') }} {{ $testingCard->bugs->count() }}</span>
                                                <span class="kanban-chip">{{ $testingCard->column?->name ?? __('Status') }}</span>
                                            </div>
                                            @if ($testingCard->started_at)
                                                <span class="text-[0.65rem] uppercase tracking-[0.2em] text-emerald-300/70">
                                                    {{ __('Started') }} {{ $testingCard->started_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </button>
                                    @empty
                                        <div class="rounded-2xl border border-emerald-400/10 bg-black/60 p-3 text-xs text-emerald-300/70">
                                            {{ __('No testing items in this column.') }}
                                        </div>
                                    @endforelse
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="soft-card overflow-hidden">
                @if ($isDevelopmentBoard)
                    <div class="grid grid-cols-6 gap-3 border-b border-emerald-400/15 px-4 py-3 text-[0.6rem] font-semibold uppercase tracking-[0.2em] text-emerald-300/70">
                        <span>{{ __('Story') }}</span>
                        <span>{{ __('Assignee') }}</span>
                        <span>{{ __('Status') }}</span>
                        <span>{{ __('Due') }}</span>
                        <span>{{ __('Priority') }}</span>
                        <span>{{ __('Signals') }}</span>
                    </div>
                    <div class="divide-y divide-emerald-400/10">
                        @forelse ($stories as $story)
                            @php
                                $assigneeName = $story->assignee?->name ?? __('Unassigned');
                                $assigneeInitials = $story->assignee
                                    ? collect(preg_split('/\s+/', trim($assigneeName)) ?: [])
                                        ->filter()
                                        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
                                        ->take(2)
                                        ->join('')
                                    : '--';
                                $columnName = $columnsById->get($story->project_board_column_id)?->name ?? __('Unknown');
                                $testingCard = $story->testingCard;
                                $testingStatus = __('Not Sent');
                                if ($testingCard) {
                                    if ($testingCard->result === TestingCard::RESULT_PASS) {
                                        $testingStatus = __('Pass');
                                    } elseif ($testingCard->result === TestingCard::RESULT_FAIL) {
                                        $testingStatus = __('Fail');
                                    } else {
                                        $testingStatus = $testingCard->column?->name ?? __('In Testing');
                                    }
                                }
                                $storyPayload = [
                                    'type' => 'story',
                                    'id' => $story->id,
                                    'issue_key' => $story->issue_key ?: sprintf('STORY-%s', $story->id),
                                    'title' => $story->title,
                                    'column_name' => $columnName,
                                    'project_board_column_id' => $story->project_board_column_id,
                                    'priority' => $story->priority,
                                    'due_date' => $story->due_date?->toDateString(),
                                    'description' => $story->description ?? '',
                                    'acceptance_criteria' => $story->acceptance_criteria ?? '',
                                    'notes' => $story->notes ?? '',
                                    'labels' => $story->labels ?? [],
                                    'labels_string' => $story->labels ? implode(', ', $story->labels) : '',
                                    'estimate' => $story->estimate,
                                    'estimate_unit' => $story->estimate_unit ?? '',
                                    'reference_links' => $story->reference_links ?? [],
                                    'reference_links_string' => $story->reference_links ? implode(PHP_EOL, $story->reference_links) : '',
                                    'database_changes' => $story->database_changes ?? [],
                                    'page_mappings' => $story->page_mappings ?? [],
                                    'database_changes_confirmed' => (bool) $story->database_changes_confirmed,
                                    'page_mappings_confirmed' => (bool) $story->page_mappings_confirmed,
                                    'blocker_reason' => $story->blocker_reason ?? '',
                                    'assignee_id' => $story->assignee_id,
                                    'assignee_name' => $assigneeName,
                                    'assignee_initials' => $assigneeInitials,
                                    'testing_status' => $testingStatus,
                                    'testing_card' => $testingCard ? [
                                        'id' => $testingCard->id,
                                        'result' => $testingCard->result,
                                        'column' => $testingCard->column?->name,
                                        'tester_name' => $testingCard->tester?->name,
                                    ] : null,
                                    'bugs' => $story->bugs->map(fn ($bug) => [
                                        'issue_key' => $bug->issue_key ?: sprintf('BUG-%s', $bug->id),
                                        'title' => $bug->title,
                                        'status' => $bug->status,
                                        'severity' => $bug->severity,
                                    ])->values(),
                                    'tasks' => $story->tasks->map(fn ($task) => [
                                        'title' => $task->title,
                                        'is_completed' => $task->is_completed,
                                    ])->values(),
                                    'created_at' => $story->created_at?->toDateTimeString(),
                                    'updated_at' => $story->updated_at?->toDateTimeString(),
                                    'latest_stage_at' => $story->latestStatusHistory?->moved_at?->toDateTimeString(),
                                    'update_url' => route('projects.kanban.boards.stories.update', [$project, $board, $story]),
                                    'assign_url' => route('projects.kanban.boards.stories.assign', [$project, $board, $story]),
                                    'move_url' => route('projects.kanban.boards.stories.move', [$project, $board, $story]),
                                    'send_to_testing_url' => route('projects.kanban.boards.stories.send-to-testing', [$project, $board, $story]),
                                    'can_send_to_testing' => $reviewColumnId !== null && $story->project_board_column_id === $reviewColumnId,
                                ];
                            @endphp
                            <button
                                type="button"
                                class="grid grid-cols-6 items-center gap-3 px-4 py-3 text-left text-xs text-emerald-100 transition hover:bg-emerald-400/5"
                                x-on:click="openDrawer(@js($storyPayload))"
                            >
                                <div class="flex flex-col gap-1 text-left">
                                    <span class="text-[0.6rem] font-semibold uppercase tracking-[0.2em] text-emerald-300/70">
                                        {{ $story->issue_key ?: sprintf('STORY-%s', $story->id) }}
                                    </span>
                                    <span class="line-clamp-2 text-sm font-semibold text-emerald-100">{{ $story->title }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border border-emerald-400/40 bg-black/80 text-[0.6rem] font-semibold text-emerald-100">
                                        {{ $assigneeInitials }}
                                    </span>
                                    <span class="truncate">{{ $assigneeName }}</span>
                                </div>
                                <span class="kanban-chip">{{ $columnName }}</span>
                                <span>{{ $story->due_date ? $story->due_date->format('M d') : __('TBD') }}</span>
                                <span class="kanban-chip">{{ $story->priority }}</span>
                                <div class="flex flex-wrap gap-2">
                                    <span class="kanban-chip">{{ __('Bugs') }} {{ $story->bugs->count() }}</span>
                                    <span class="kanban-chip">{{ __('Testing') }}: {{ $testingStatus }}</span>
                                </div>
                            </button>
                        @empty
                            <div class="px-4 py-8 text-sm text-emerald-300/70">{{ __('No stories match the current filters.') }}</div>
                        @endforelse
                    </div>
                @else
                    <div class="grid grid-cols-5 gap-3 border-b border-emerald-400/15 px-4 py-3 text-[0.6rem] font-semibold uppercase tracking-[0.2em] text-emerald-300/70">
                        <span>{{ __('Testing Item') }}</span>
                        <span>{{ __('Tester') }}</span>
                        <span>{{ __('Status') }}</span>
                        <span>{{ __('Result') }}</span>
                        <span>{{ __('Signals') }}</span>
                    </div>
                    <div class="divide-y divide-emerald-400/10">
                        @forelse ($testingCards as $testingCard)
                            @php
                                $story = $testingCard->story;
                                $storyIssue = $story?->issue_key ?: ($story ? sprintf('STORY-%s', $story->id) : __('Story'));
                                $testerName = $testingCard->tester?->name ?? __('Unassigned');
                                $testerInitials = $testingCard->tester
                                    ? collect(preg_split('/\s+/', trim($testerName)) ?: [])
                                        ->filter()
                                        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
                                        ->take(2)
                                        ->join('')
                                    : '--';
                                $resultLabel = $testingCard->result === TestingCard::RESULT_PASS
                                    ? __('Pass')
                                    : ($testingCard->result === TestingCard::RESULT_FAIL ? __('Fail') : __('Pending'));
                                $testingPayload = [
                                    'type' => 'testing',
                                    'id' => $testingCard->id,
                                    'project_board_column_id' => $testingCard->project_board_column_id,
                                    'column_name' => $testingCard->column?->name ?? __('Unknown'),
                                    'result' => $testingCard->result,
                                    'notes' => $testingCard->notes ?? '',
                                    'tester_id' => $testingCard->tester_id,
                                    'tester_name' => $testerName,
                                    'tester_initials' => $testerInitials,
                                    'story_id' => $story?->id,
                                    'story_issue_key' => $storyIssue,
                                    'story_title' => $story?->title,
                                    'story_assignee' => $story?->assignee?->name,
                                    'due_date' => $story?->due_date?->toDateString(),
                                    'bugs' => $testingCard->bugs->map(fn ($bug) => [
                                        'issue_key' => $bug->issue_key ?: sprintf('BUG-%s', $bug->id),
                                        'title' => $bug->title,
                                        'status' => $bug->status,
                                        'severity' => $bug->severity,
                                    ])->values(),
                                    'created_at' => $testingCard->created_at?->toDateTimeString(),
                                    'updated_at' => $testingCard->updated_at?->toDateTimeString(),
                                    'started_at' => $testingCard->started_at?->toDateTimeString(),
                                    'completed_at' => $testingCard->completed_at?->toDateTimeString(),
                                    'assign_url' => route('projects.kanban.boards.testing-cards.assign', [$project, $board, $testingCard]),
                                    'move_url' => route('projects.kanban.boards.testing-cards.move', [$project, $board, $testingCard]),
                                    'result_url' => route('projects.kanban.boards.testing-cards.result', [$project, $board, $testingCard]),
                                ];
                            @endphp
                            <button
                                type="button"
                                class="grid grid-cols-5 items-center gap-3 px-4 py-3 text-left text-xs text-emerald-100 transition hover:bg-emerald-400/5"
                                x-on:click="openDrawer(@js($testingPayload))"
                            >
                                <div class="flex flex-col gap-1">
                                    <span class="text-[0.6rem] font-semibold uppercase tracking-[0.2em] text-emerald-300/70">
                                        {{ $storyIssue }}
                                    </span>
                                    <span class="line-clamp-2 text-sm font-semibold text-emerald-100">{{ $story?->title ?? __('Testing Item') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border border-emerald-400/40 bg-black/80 text-[0.6rem] font-semibold text-emerald-100">
                                        {{ $testerInitials }}
                                    </span>
                                    <span class="truncate">{{ $testerName }}</span>
                                </div>
                                <span class="kanban-chip">{{ $testingCard->column?->name ?? __('Status') }}</span>
                                <span class="kanban-chip">{{ $resultLabel }}</span>
                                <div class="flex flex-wrap gap-2">
                                    <span class="kanban-chip">{{ __('Bugs') }} {{ $testingCard->bugs->count() }}</span>
                                    <span class="kanban-chip">{{ $story?->due_date ? $story->due_date->format('M d') : __('TBD') }}</span>
                                </div>
                            </button>
                        @empty
                            <div class="px-4 py-8 text-sm text-emerald-300/70">{{ __('No testing cards match the current filters.') }}</div>
                        @endforelse
                    </div>
                @endif
            </div>
        @endif
        @if ($documents->isNotEmpty())
            <div class="soft-card p-4">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Generated Documents') }}</h2>
                        <span class="text-xs text-emerald-300/70">{{ $documents->count() }} {{ __('files') }}</span>
                    </div>
                    <div class="flex flex-col gap-3">
                        @foreach ($documents as $document)
                            <div class="flex flex-col gap-2 rounded-2xl border border-emerald-400/20 bg-black/70 p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300/80">{{ $document->type }}</span>
                                        <span class="text-sm font-semibold text-emerald-100">{{ $document->file_name }}</span>
                                    </div>
                                    <a href="{{ route('projects.kanban.boards.documents.download', [$project, $board, $document]) }}" class="kanban-button">
                                        {{ __('Download') }}
                                    </a>
                                </div>
                                <span class="text-xs text-emerald-300/70">
                                    {{ __('Generated') }} {{ $document->generated_at?->toDayDateTimeString() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div x-cloak x-show="drawerOpen" class="fixed inset-0 z-50 flex items-stretch justify-end">
            <div class="absolute inset-0 bg-black/80" x-on:click="drawerOpen = false"></div>
            <div class="relative h-full w-full max-w-lg overflow-y-auto border-l border-emerald-400/30 bg-black/95 p-6 shadow-[0_0_40px_rgba(0,0,0,0.8)]">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-300/70" x-text="drawerItem?.issue_key || drawerItem?.story_issue_key || '{{ __('Item') }}'"></span>
                        <h2 class="text-lg font-semibold text-emerald-100" x-text="drawerItem?.title || drawerItem?.story_title"></h2>
                    </div>
                    <button type="button" class="kanban-button kanban-button-ghost" x-on:click="drawerOpen = false">
                        {{ __('Close') }}
                    </button>
                </div>

                <template x-if="drawerItem && drawerItem.type === 'story'">
                    <div class="mt-6 flex flex-col gap-6">
                        <div class="flex flex-wrap gap-2">
                            <span class="kanban-chip" x-text="drawerItem.column_name"></span>
                            <span class="kanban-chip" x-text="drawerItem.priority"></span>
                            <span class="kanban-chip" x-text="drawerItem.testing_status"></span>
                        </div>

                        <div class="flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Module Metadata') }}</h3>
                            <div class="flex flex-col gap-2 text-xs text-emerald-200/80">
                                <span>{{ __('Type') }}: {{ __('Story') }}</span>
                                <span>{{ __('Project') }}: {{ $project->name }}</span>
                                <span x-text="'{{ __('Created') }}: ' + (drawerItem.created_at || '{{ __('-') }}')"></span>
                                <span x-text="'{{ __('Updated') }}: ' + (drawerItem.updated_at || '{{ __('-') }}')"></span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Summary') }}</h3>
                            <div class="flex flex-col gap-3 text-xs text-emerald-200/80">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full border border-emerald-400/40 bg-black/80 text-sm font-semibold text-emerald-100" x-text="drawerItem.assignee_initials"></span>
                                    <div class="flex flex-col">
                                        <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Assignee') }}</span>
                                        <span class="text-sm text-emerald-100" x-text="drawerItem.assignee_name"></span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <span>{{ __('Due') }}: <span x-text="drawerItem.due_date || '{{ __('TBD') }}'"></span></span>
                                    <span x-text="'{{ __('Labels') }}: ' + (drawerItem.labels.length ? drawerItem.labels.join(', ') : '{{ __('None') }}')"></span>
                                    <span x-text="'{{ __('Estimate') }}: ' + (drawerItem.estimate ? drawerItem.estimate + ' ' + drawerItem.estimate_unit : '{{ __('TBD') }}')"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Story Details') }}</h3>
                            <div class="flex flex-col gap-3 text-xs text-emerald-200/80">
                                <div>
                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Description') }}</span>
                                    <p class="mt-1 text-sm text-emerald-100 whitespace-pre-line" x-text="drawerItem.description || '{{ __('No description added.') }}'"></p>
                                </div>
                                <div>
                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Acceptance Criteria') }}</span>
                                    <p class="mt-1 text-sm text-emerald-100 whitespace-pre-line" x-text="drawerItem.acceptance_criteria || '{{ __('No acceptance criteria provided.') }}'"></p>
                                </div>
                                <div>
                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Notes') }}</span>
                                    <p class="mt-1 text-sm text-emerald-100 whitespace-pre-line" x-text="drawerItem.notes || '{{ __('No notes added.') }}'"></p>
                                </div>
                                <div>
                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Reference Links') }}</span>
                                    <div class="mt-2 flex flex-col gap-2">
                                        <template x-if="drawerItem.reference_links.length === 0">
                                            <span class="text-xs text-emerald-300/70">{{ __('No links recorded.') }}</span>
                                        </template>
                                        <template x-for="link in drawerItem.reference_links" :key="link">
                                            <a :href="link" class="text-sm text-emerald-100 underline decoration-emerald-400/60" target="_blank" rel="noopener">
                                                <span x-text="link"></span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Linked Work') }}</h3>
                            <div class="flex flex-col gap-3 text-xs text-emerald-200/80">
                                <div>
                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Tasks') }}</span>
                                    <div class="mt-2 flex flex-col gap-2">
                                        <template x-if="drawerItem.tasks.length === 0">
                                            <span class="text-xs text-emerald-300/70">{{ __('No tasks linked.') }}</span>
                                        </template>
                                        <template x-for="task in drawerItem.tasks" :key="task.title">
                                            <div class="flex items-center gap-2 text-sm text-emerald-100">
                                                <span class="text-emerald-300/70" x-text="task.is_completed ? '[x]' : '[ ]'"></span>
                                                <span x-text="task.title"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Bugs') }}</span>
                                    <div class="mt-2 flex flex-col gap-2">
                                        <template x-if="drawerItem.bugs.length === 0">
                                            <span class="text-xs text-emerald-300/70">{{ __('No linked bugs.') }}</span>
                                        </template>
                                        <template x-for="bug in drawerItem.bugs" :key="bug.issue_key">
                                            <div class="flex items-center justify-between gap-2 text-sm text-emerald-100">
                                                <div class="flex flex-col">
                                                    <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70" x-text="bug.issue_key"></span>
                                                    <span x-text="bug.title"></span>
                                                </div>
                                                <span class="kanban-chip" x-text="bug.status"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Database Changes') }}</h3>
                            <div class="flex flex-col gap-3">
                                <template x-if="drawerItem.database_changes.length === 0">
                                    <span class="text-xs text-emerald-300/70">{{ __('No database changes recorded.') }}</span>
                                </template>
                                <template x-for="change in drawerItem.database_changes" :key="change.table + change.field + change.timestamp">
                                    <div class="rounded-2xl border border-emerald-400/15 bg-black/70 p-3 text-xs text-emerald-200/80">
                                        <div class="flex flex-wrap gap-3">
                                            <span x-text="'{{ __('Table') }}: ' + (change.table || '-')"></span>
                                            <span x-text="'{{ __('Field') }}: ' + (change.field || '-')"></span>
                                            <span x-text="'{{ __('Action') }}: ' + (change.action || '-')"></span>
                                            <span x-text="'{{ __('Timestamp') }}: ' + (change.timestamp || '-')"></span>
                                        </div>
                                        <p class="mt-2 text-sm text-emerald-100 whitespace-pre-line" x-text="change.notes || '{{ __('No notes provided.') }}'"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Page Mappings') }}</h3>
                            <div class="flex flex-col gap-3">
                                <template x-if="drawerItem.page_mappings.length === 0">
                                    <span class="text-xs text-emerald-300/70">{{ __('No page mappings recorded.') }}</span>
                                </template>
                                <template x-for="mapping in drawerItem.page_mappings" :key="mapping.page + mapping.route + mapping.component">
                                    <div class="rounded-2xl border border-emerald-400/15 bg-black/70 p-3 text-xs text-emerald-200/80">
                                        <div class="flex flex-wrap gap-3">
                                            <span x-text="'{{ __('Page') }}: ' + (mapping.page || '-')"></span>
                                            <span x-text="'{{ __('Route') }}: ' + (mapping.route || '-')"></span>
                                            <span x-text="'{{ __('Component') }}: ' + (mapping.component || '-')"></span>
                                            <span x-text="'{{ __('Models') }}: ' + (mapping.models || '-')"></span>
                                        </div>
                                        <p class="mt-2 text-sm text-emerald-100 whitespace-pre-line" x-text="mapping.notes || '{{ __('No notes provided.') }}'"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="flex flex-col gap-4 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Update Story') }}</h3>
                            <form method="POST" x-bind:action="drawerItem.update_url" class="flex flex-col gap-4">
                                @csrf
                                @method('PATCH')
                                <div class="flex flex-col gap-2">
                                    <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Title') }}</label>
                                    <input type="text" name="title" class="kanban-input" x-model="drawerItem.title">
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Description') }}</label>
                                    <textarea name="description" rows="3" class="kanban-textarea" x-model="drawerItem.description"></textarea>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Acceptance Criteria') }}</label>
                                    <textarea name="acceptance_criteria" rows="3" class="kanban-textarea" x-model="drawerItem.acceptance_criteria"></textarea>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Notes') }}</label>
                                    <textarea name="notes" rows="2" class="kanban-textarea" x-model="drawerItem.notes"></textarea>
                                </div>
                                <div class="grid gap-3 md:grid-cols-2">
                                    <div class="flex flex-col gap-2">
                                        <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Due Date') }}</label>
                                        <input type="date" name="due_date" class="kanban-input" x-model="drawerItem.due_date">
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Priority') }}</label>
                                        <select name="priority" class="kanban-select" x-model="drawerItem.priority">
                                            @foreach (Story::PRIORITIES as $priority)
                                                <option value="{{ $priority }}">{{ $priority }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="grid gap-3 md:grid-cols-2">
                                    <div class="flex flex-col gap-2">
                                        <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Labels') }}</label>
                                        <input type="text" name="labels" class="kanban-input" placeholder="{{ __('UI, API, Layout') }}" x-model="drawerItem.labels_string">
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Estimate') }}</label>
                                        <div class="flex gap-2">
                                            <input type="number" name="estimate" min="0" class="kanban-input" x-model="drawerItem.estimate">
                                            <select name="estimate_unit" class="kanban-select" x-model="drawerItem.estimate_unit">
                                                <option value="points">{{ __('Points') }}</option>
                                                <option value="hours">{{ __('Hours') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Links / Attachments') }}</label>
                                    <textarea name="reference_links" rows="2" class="kanban-textarea" placeholder="{{ __('One link per line') }}" x-model="drawerItem.reference_links_string"></textarea>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Database Changes') }}</span>
                                        <button type="button" class="kanban-button kanban-button-ghost" x-on:click="drawerItem.database_changes.push({ table: '', field: '', action: '', notes: '', timestamp: '' })">
                                            {{ __('Add') }}
                                        </button>
                                    </div>
                                    <template x-for="(change, index) in drawerItem.database_changes" :key="index">
                                        <div class="flex flex-col gap-2 rounded-2xl border border-emerald-400/15 bg-black/70 p-3">
                                            <div class="grid gap-2 md:grid-cols-2">
                                                <input type="text" class="kanban-input" placeholder="{{ __('Table') }}" x-model="change.table" x-bind:name="`database_changes[${index}][table]`">
                                                <input type="text" class="kanban-input" placeholder="{{ __('Field') }}" x-model="change.field" x-bind:name="`database_changes[${index}][field]`">
                                                <input type="text" class="kanban-input" placeholder="{{ __('Action') }}" x-model="change.action" x-bind:name="`database_changes[${index}][action]`">
                                                <input type="datetime-local" class="kanban-input" x-model="change.timestamp" x-bind:name="`database_changes[${index}][timestamp]`">
                                            </div>
                                            <textarea rows="2" class="kanban-textarea" placeholder="{{ __('Notes') }}" x-model="change.notes" x-bind:name="`database_changes[${index}][notes]`"></textarea>
                                            <button type="button" class="kanban-button kanban-button-ghost" x-on:click="drawerItem.database_changes.splice(index, 1)">
                                                {{ __('Remove') }}
                                            </button>
                                        </div>
                                    </template>
                                    <input type="hidden" name="database_changes_confirmed" x-bind:value="drawerItem.database_changes_confirmed ? 1 : 0">
                                    <label class="flex items-center gap-2 text-xs text-emerald-200/80">
                                        <input type="checkbox" class="h-4 w-4 rounded border-emerald-400/40 bg-black/70 text-emerald-400" x-model="drawerItem.database_changes_confirmed">
                                        <span>{{ __('Database changes confirmed') }}</span>
                                    </label>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Page Mappings') }}</span>
                                        <button type="button" class="kanban-button kanban-button-ghost" x-on:click="drawerItem.page_mappings.push({ page: '', route: '', component: '', models: '', notes: '' })">
                                            {{ __('Add') }}
                                        </button>
                                    </div>
                                    <template x-for="(mapping, index) in drawerItem.page_mappings" :key="index">
                                        <div class="flex flex-col gap-2 rounded-2xl border border-emerald-400/15 bg-black/70 p-3">
                                            <div class="grid gap-2 md:grid-cols-2">
                                                <input type="text" class="kanban-input" placeholder="{{ __('Page') }}" x-model="mapping.page" x-bind:name="`page_mappings[${index}][page]`">
                                                <input type="text" class="kanban-input" placeholder="{{ __('Route') }}" x-model="mapping.route" x-bind:name="`page_mappings[${index}][route]`">
                                                <input type="text" class="kanban-input" placeholder="{{ __('Component') }}" x-model="mapping.component" x-bind:name="`page_mappings[${index}][component]`">
                                                <input type="text" class="kanban-input" placeholder="{{ __('Models') }}" x-model="mapping.models" x-bind:name="`page_mappings[${index}][models]`">
                                            </div>
                                            <textarea rows="2" class="kanban-textarea" placeholder="{{ __('Notes') }}" x-model="mapping.notes" x-bind:name="`page_mappings[${index}][notes]`"></textarea>
                                            <button type="button" class="kanban-button kanban-button-ghost" x-on:click="drawerItem.page_mappings.splice(index, 1)">
                                                {{ __('Remove') }}
                                            </button>
                                        </div>
                                    </template>
                                    <input type="hidden" name="page_mappings_confirmed" x-bind:value="drawerItem.page_mappings_confirmed ? 1 : 0">
                                    <label class="flex items-center gap-2 text-xs text-emerald-200/80">
                                        <input type="checkbox" class="h-4 w-4 rounded border-emerald-400/40 bg-black/70 text-emerald-400" x-model="drawerItem.page_mappings_confirmed">
                                        <span>{{ __('Page mappings confirmed') }}</span>
                                    </label>
                                </div>
                                <button type="submit" class="kanban-button kanban-button-primary">{{ __('Save Updates') }}</button>
                            </form>
                        </div>
                        <div class="flex flex-col gap-4 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Story Actions') }}</h3>
                            <form method="POST" x-bind:action="drawerItem.assign_url" class="flex flex-col gap-3">
                                @csrf
                                <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Assign Developer') }}</label>
                                <select name="assignee_id" class="kanban-select" x-model="drawerItem.assignee_id">
                                    <option value="">{{ __('Select developer') }}</option>
                                    @foreach ($developers as $developer)
                                        <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="kanban-button">{{ __('Assign') }}</button>
                            </form>

                            <form method="POST" x-bind:action="drawerItem.move_url" class="flex flex-col gap-3">
                                @csrf
                                <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Move Story') }}</label>
                                <select
                                    name="column_id"
                                    class="kanban-select"
                                    x-model="moveColumnId"
                                    x-on:change="if (moveColumnId != blockerColumnId) { blockerReason = ''; }"
                                >
                                    @foreach ($columns as $column)
                                        <option value="{{ $column->id }}">{{ $column->name }}</option>
                                    @endforeach
                                </select>
                                <div x-show="moveColumnId == blockerColumnId" class="flex flex-col gap-2">
                                    <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Blocker Reason') }}</label>
                                    <select name="blocker_reason" class="kanban-select" x-model="blockerReason">
                                        <option value="">{{ __('Select reason') }}</option>
                                        @foreach (Story::BLOCKER_REASON_LABELS as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="text" name="reason" class="kanban-input" placeholder="{{ __('Reason (optional)') }}">
                                <textarea name="notes" rows="2" class="kanban-textarea" placeholder="{{ __('Notes (optional)') }}"></textarea>
                                <button type="submit" class="kanban-button">{{ __('Move') }}</button>
                            </form>

                            <form method="POST" x-bind:action="drawerItem.send_to_testing_url" x-show="drawerItem.can_send_to_testing" class="flex flex-col gap-3">
                                @csrf
                                <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Send to Testing') }}</label>
                                <select name="tester_id" class="kanban-select">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach ($testers as $tester)
                                        <option value="{{ $tester->id }}">{{ $tester->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="kanban-button">{{ __('Send') }}</button>
                            </form>
                        </div>
                    </div>
                </template>
                <template x-if="drawerItem && drawerItem.type === 'testing'">
                    <div class="mt-6 flex flex-col gap-6">
                        <div class="flex flex-wrap gap-2">
                            <span class="kanban-chip" x-text="drawerItem.column_name"></span>
                            <span class="kanban-chip" x-text="drawerItem.result ? drawerItem.result.toUpperCase() : '{{ __('Pending') }}'"></span>
                        </div>

                        <div class="flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Module Metadata') }}</h3>
                            <div class="flex flex-col gap-2 text-xs text-emerald-200/80">
                                <span>{{ __('Type') }}: {{ __('Testing') }}</span>
                                <span>{{ __('Project') }}: {{ $project->name }}</span>
                                <span x-text="'{{ __('Created') }}: ' + (drawerItem.created_at || '{{ __('-') }}')"></span>
                                <span x-text="'{{ __('Updated') }}: ' + (drawerItem.updated_at || '{{ __('-') }}')"></span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Testing Summary') }}</h3>
                            <div class="flex flex-col gap-3 text-xs text-emerald-200/80">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full border border-emerald-400/40 bg-black/80 text-sm font-semibold text-emerald-100" x-text="drawerItem.tester_initials"></span>
                                    <div class="flex flex-col">
                                        <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Tester') }}</span>
                                        <span class="text-sm text-emerald-100" x-text="drawerItem.tester_name"></span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <span x-text="'{{ __('Story') }}: ' + (drawerItem.story_issue_key || '{{ __('-') }}')"></span>
                                    <span x-text="'{{ __('Due') }}: ' + (drawerItem.due_date || '{{ __('TBD') }}')"></span>
                                    <span x-text="'{{ __('Started') }}: ' + (drawerItem.started_at || '{{ __('-') }}')"></span>
                                    <span x-text="'{{ __('Completed') }}: ' + (drawerItem.completed_at || '{{ __('-') }}')"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Linked Bugs') }}</h3>
                            <div class="flex flex-col gap-2">
                                <template x-if="drawerItem.bugs.length === 0">
                                    <span class="text-xs text-emerald-300/70">{{ __('No linked bugs.') }}</span>
                                </template>
                                <template x-for="bug in drawerItem.bugs" :key="bug.issue_key">
                                    <div class="flex items-center justify-between gap-2 text-sm text-emerald-100">
                                        <div class="flex flex-col">
                                            <span class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70" x-text="bug.issue_key"></span>
                                            <span x-text="bug.title"></span>
                                        </div>
                                        <span class="kanban-chip" x-text="bug.status"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="flex flex-col gap-4 rounded-2xl border border-emerald-400/20 bg-black/70 p-4">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">{{ __('Testing Actions') }}</h3>
                            <form method="POST" x-bind:action="drawerItem.assign_url" class="flex flex-col gap-3">
                                @csrf
                                <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Assign Tester') }}</label>
                                <select name="tester_id" class="kanban-select" x-model="drawerItem.tester_id">
                                    <option value="">{{ __('Select tester') }}</option>
                                    @foreach ($testers as $tester)
                                        <option value="{{ $tester->id }}">{{ $tester->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="kanban-button">{{ __('Assign') }}</button>
                            </form>

                            <form method="POST" x-bind:action="drawerItem.move_url" class="flex flex-col gap-3">
                                @csrf
                                <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Move Testing Card') }}</label>
                                <select name="column_id" class="kanban-select" x-model="testingMoveColumnId">
                                    @foreach ($columns as $column)
                                        <option value="{{ $column->id }}">{{ $column->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="kanban-button">{{ __('Move') }}</button>
                            </form>

                            <form method="POST" x-bind:action="drawerItem.result_url" class="flex flex-col gap-3">
                                @csrf
                                <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Record Result') }}</label>
                                <select name="result" class="kanban-select" x-model="testingResult">
                                    <option value="">{{ __('Select result') }}</option>
                                    <option value="{{ TestingCard::RESULT_PASS }}">{{ __('Pass') }}</option>
                                    <option value="{{ TestingCard::RESULT_FAIL }}">{{ __('Fail') }}</option>
                                </select>
                                <textarea name="notes" rows="2" class="kanban-textarea" placeholder="{{ __('Notes (optional)') }}"></textarea>
                                <div x-show="testingResult === '{{ TestingCard::RESULT_FAIL }}'" class="flex flex-col gap-3">
                                    <input type="text" name="bug_title" class="kanban-input" placeholder="{{ __('Bug title') }}">
                                    <select name="bug_severity" class="kanban-select">
                                        <option value="">{{ __('Severity') }}</option>
                                        @foreach (Bug::SEVERITIES as $severity)
                                            <option value="{{ $severity }}">{{ $severity }}</option>
                                        @endforeach
                                    </select>
                                    <textarea name="bug_description" rows="2" class="kanban-textarea" placeholder="{{ __('Bug description') }}"></textarea>
                                    <textarea name="bug_steps" rows="2" class="kanban-textarea" placeholder="{{ __('Steps to reproduce') }}"></textarea>
                                    <select name="bug_assignee_id" class="kanban-select">
                                        <option value="">{{ __('Assign developer (optional)') }}</option>
                                        @foreach ($developers as $developer)
                                            <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="kanban-button">{{ __('Save Result') }}</button>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div x-cloak x-show="showCreateStory" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
            <div class="absolute inset-0 bg-black/80" x-on:click="showCreateStory = false"></div>
            <div class="relative w-full max-w-4xl rounded-3xl border border-emerald-400/30 bg-black/95 p-6 shadow-[0_0_40px_rgba(0,0,0,0.8)]">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-300/70">{{ __('Development') }}</span>
                        <h2 class="text-lg font-semibold text-emerald-100">{{ __('Create Story') }}</h2>
                    </div>
                    <button type="button" class="kanban-button kanban-button-ghost" x-on:click="showCreateStory = false">
                        {{ __('Close') }}
                    </button>
                </div>
                <form method="POST" action="{{ route('projects.kanban.boards.stories.store', [$project, $board]) }}" class="mt-6 flex flex-col gap-4">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Title') }}</label>
                            <input type="text" name="title" required class="kanban-input" placeholder="{{ __('Story title') }}">
                        </div>
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Description') }}</label>
                            <textarea name="description" rows="3" required class="kanban-textarea" placeholder="{{ __('Describe the work...') }}"></textarea>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Due Date') }}</label>
                            <input type="date" name="due_date" required class="kanban-input">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Assign to') }}</label>
                            <select name="assignee_id" required class="kanban-select">
                                <option value="">{{ __('Select developer') }}</option>
                                @foreach ($developers as $developer)
                                    <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Priority') }}</label>
                            <select name="priority" required class="kanban-select">
                                @foreach (Story::PRIORITIES as $priority)
                                    <option value="{{ $priority }}">{{ $priority }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Estimate') }}</label>
                            <div class="flex gap-2">
                                <input type="number" name="estimate" min="0" class="kanban-input" placeholder="{{ __('Points') }}">
                                <select name="estimate_unit" class="kanban-select">
                                    <option value="points">{{ __('Points') }}</option>
                                    <option value="hours">{{ __('Hours') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Acceptance Criteria') }}</label>
                            <textarea name="acceptance_criteria" rows="2" class="kanban-textarea" placeholder="{{ __('Recommended for review readiness') }}"></textarea>
                        </div>
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Labels / Tags') }}</label>
                            <input type="text" name="labels" class="kanban-input" placeholder="{{ __('UI, API, Layout') }}">
                        </div>
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Attachments / Links') }}</label>
                            <textarea name="reference_links" rows="2" class="kanban-textarea" placeholder="{{ __('One link per line') }}"></textarea>
                        </div>
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Notes') }}</label>
                            <textarea name="notes" rows="2" class="kanban-textarea" placeholder="{{ __('Optional notes') }}"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="kanban-button kanban-button-ghost" x-on:click="showCreateStory = false">{{ __('Cancel') }}</button>
                        <button type="submit" class="kanban-button kanban-button-primary">{{ __('Create Story') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div x-cloak x-show="showCreateBug" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
            <div class="absolute inset-0 bg-black/80" x-on:click="showCreateBug = false"></div>
            <div class="relative w-full max-w-3xl rounded-3xl border border-emerald-400/30 bg-black/95 p-6 shadow-[0_0_40px_rgba(0,0,0,0.8)]">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-300/70">{{ __('Testing') }}</span>
                        <h2 class="text-lg font-semibold text-emerald-100">{{ __('Create Bug/Issue') }}</h2>
                    </div>
                    <button type="button" class="kanban-button kanban-button-ghost" x-on:click="showCreateBug = false">
                        {{ __('Close') }}
                    </button>
                </div>
                <form method="POST" action="{{ route('projects.kanban.boards.bugs.store', [$project, $board]) }}" class="mt-6 flex flex-col gap-4">
                    @csrf
                    <div class="flex flex-col gap-2">
                        <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Testing Item') }}</label>
                        <select name="testing_card_id" required class="kanban-select">
                            <option value="">{{ __('Select testing card') }}</option>
                            @foreach ($testingCards as $card)
                                <option value="{{ $card->id }}">
                                    {{ $card->story?->issue_key ?: sprintf('STORY-%s', $card->story?->id) }} - {{ $card->story?->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Bug Title') }}</label>
                        <input type="text" name="title" required class="kanban-input" placeholder="{{ __('Bug title') }}">
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Severity') }}</label>
                            <select name="severity" required class="kanban-select">
                                @foreach (Bug::SEVERITIES as $severity)
                                    <option value="{{ $severity }}">{{ $severity }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Assign Developer') }}</label>
                            <select name="assignee_id" class="kanban-select">
                                <option value="">{{ __('Optional') }}</option>
                                @foreach ($developers as $developer)
                                    <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Description') }}</label>
                        <textarea name="description" rows="3" class="kanban-textarea" placeholder="{{ __('Describe the bug...') }}"></textarea>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[0.6rem] uppercase tracking-[0.2em] text-emerald-300/70">{{ __('Steps to Reproduce') }}</label>
                        <textarea name="steps_to_reproduce" rows="3" class="kanban-textarea" placeholder="{{ __('Steps to reproduce...') }}"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="kanban-button kanban-button-ghost" x-on:click="showCreateBug = false">{{ __('Cancel') }}</button>
                        <button type="submit" class="kanban-button kanban-button-primary">{{ __('Create Bug') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-kanban-layout>

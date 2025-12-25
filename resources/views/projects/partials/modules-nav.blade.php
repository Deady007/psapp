@php
    $developmentBoard = $project->developmentBoard;
    $testingBoard = $project->testingBoard;
    $currentBoard = request()->route('board');
    $isDevelopmentBoard = $currentBoard instanceof \App\Models\ProjectBoard && $currentBoard->isDevelopment();
    $isTestingBoard = $currentBoard instanceof \App\Models\ProjectBoard && $currentBoard->isTesting();
@endphp

<div class="card card-outline card-primary">
    <div class="card-body">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link @if (request()->routeIs('projects.show')) active @endif" href="{{ route('projects.show', $project) }}">
                    {{ __('Overview') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if (request()->routeIs('projects.kickoffs.*')) active @endif" href="{{ route('projects.kickoffs.show', $project) }}">
                    {{ __('Kick-off') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if (request()->routeIs('projects.requirements.*')) active @endif" href="{{ route('projects.requirements.index', $project) }}">
                    {{ __('Requirements') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if (request()->routeIs('projects.drive-documents.*')) active @endif" href="{{ route('projects.drive-documents.index', $project) }}">
                    {{ __('Drive Documents') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if ($isDevelopmentBoard) active @endif" href="{{ $developmentBoard ? route('projects.kanban.boards.show', [$project, $developmentBoard]) : route('projects.kanban.index', $project) }}">
                    {{ __('Development') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if ($isTestingBoard) active @endif" href="{{ $testingBoard ? route('projects.kanban.boards.show', [$project, $testingBoard]) : route('projects.kanban.index', $project) }}">
                    {{ __('Testing') }}
                </a>
            </li>
        </ul>
    </div>
</div>

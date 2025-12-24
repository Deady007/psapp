<div class="card module-nav">
    <div class="card-body py-2">
        <ul class="nav nav-pills flex-wrap" role="tablist" aria-label="{{ __('Project modules') }}">
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
        </ul>
    </div>
</div>

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
                <a class="nav-link @if (request()->routeIs('projects.documents.*')) active @endif" href="{{ route('projects.documents.index', $project) }}">
                    {{ __('Documents') }}
                </a>
            </li>
        </ul>
    </div>
</div>

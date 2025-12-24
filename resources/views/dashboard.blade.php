<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-start">
            <div class="mb-3 mb-sm-0">
                <div class="text-uppercase small text-muted font-weight-bold mb-1">{{ __('Control center') }}</div>
                <h1 class="m-0">{{ __('Dashboard') }}</h1>
                <div class="text-muted">{{ __('Pulse check on customers, projects, kickoffs, and requirements in one view.') }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('projects.index') }}" class="btn btn-primary mr-2 mb-2">
                    <i class="fas fa-plus mr-1"></i>
                    {{ __('New Project') }}
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary mb-2">
                    <i class="fas fa-user-friends mr-1"></i>
                    {{ __('View Customers') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-lg-4 col-md-6">
            <x-adminlte-small-box
                title="0"
                text="{{ __('Customers') }}"
                icon="fas fa-users"
                theme="info"
                url="{{ route('customers.index') }}"
                urlText="{{ __('View customers') }}"
            />
        </div>
        <div class="col-lg-4 col-md-6">
            <x-adminlte-small-box
                title="0"
                text="{{ __('Projects') }}"
                icon="fas fa-briefcase"
                theme="success"
                url="{{ route('projects.index') }}"
                urlText="{{ __('View projects') }}"
            />
        </div>
        <div class="col-lg-4 col-md-6">
            <x-adminlte-small-box
                title="0"
                text="{{ __('Team') }}"
                icon="fas fa-user-friends"
                theme="warning"
            />
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Momentum') }}</h3>
            <span class="badge badge-info ml-2">{{ __('Live workspace') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                            <div class="quick-action">
                        <div class="icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold">{{ __('Kickoff calls ready') }}</div>
                            <div class="text-muted small mb-1">{{ __('Prep, schedule, and capture outcomes without page refreshes.') }}</div>
                            <a href="{{ route('projects.index') }}" class="small font-weight-bold">
                                {{ __('Open kickoff workspace') }} →
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="quick-action">
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold">{{ __('Requirements room') }}</div>
                            <div class="text-muted small mb-1">{{ __('Collect, prioritize, and export RFP-ready docs in one flow.') }}</div>
                            <a href="{{ route('projects.index') }}" class="small font-weight-bold">
                                {{ __('View requirements') }} →
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="quick-action">
                        <div class="icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold">{{ __('Drive documents') }}</div>
                            <div class="text-muted small mb-1">{{ __('Drag, rename, move, and approve files with consistent UI.') }}</div>
                            <a href="{{ route('projects.index') }}" class="small font-weight-bold">
                                {{ __('Open documents') }} →
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="quick-action">
                        <div class="icon">
                            <i class="fas fa-people-carry"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold">{{ __('Customers + contacts') }}</div>
                            <div class="text-muted small mb-1">{{ __('View customers and contacts together to avoid reloads.') }}</div>
                            <a href="{{ route('customers.index') }}" class="small font-weight-bold">
                                {{ __('Go to customers') }} →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

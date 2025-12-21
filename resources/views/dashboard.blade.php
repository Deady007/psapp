<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Dashboard') }}</h1>
                <div class="text-muted">{{ __('Pulse check on customers, projects, and team momentum.') }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('projects.index') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    {{ __('New Project') }}
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
            <h3 class="card-title">{{ __('Quick Actions') }}</h3>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                {{ __('Use the shortcuts below to keep momentum with smooth transitions.') }}
            </p>
            <div class="btn-group" role="group" aria-label="{{ __('Quick actions') }}">
                <a href="{{ route('customers.index') }}" class="btn btn-outline-primary">
                    {{ __('View Customers') }}
                </a>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                    {{ __('View Projects') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

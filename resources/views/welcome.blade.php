@extends('adminlte::master')

@section('title', config('branding.name', config('app.name', 'Laravel')))
@section('classes_body', 'layout-top-nav')

@section('body')
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white border-bottom">
            <div class="container">
                <a href="{{ url('/') }}" class="navbar-brand">
                    <img src="{{ asset(config('branding.logo_path')) }}" alt="{{ config('branding.logo_alt', 'Logo') }}" class="brand-image img-circle elevation-2">
                    <span class="brand-text font-weight-bold">{{ config('branding.name', config('app.name', 'Laravel')) }}</span>
                </a>

                <ul class="navbar-nav ml-auto">
                    @auth
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link">{{ __('Dashboard') }}</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link">{{ __('Sign In') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item ml-md-2">
                                <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Get Started') }}</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </nav>

        <div class="content-wrapper">
            <div class="content" id="main-content">
                <div class="container">
                    <div class="landing-hero mb-4">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="eyebrow mb-3">
                                    <i class="fas fa-wave-square"></i>
                                    {{ __('Project delivery workspace') }}
                                </div>
                                <h1 class="display-4 font-weight-bold">
                                    {{ __('Operational clarity for project teams.') }}
                                </h1>
                                <p class="hero-subtext lead mb-0">
                                    {{ __('Plan kickoffs, capture requirements, and keep documents, clients, and approvals together without losing context.') }}
                                </p>
                                <div class="hero-actions mt-4">
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                            <i class="fas fa-rocket"></i>
                                            {{ __('Start free') }}
                                        </a>
                                    @endif
                                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-sign-in-alt"></i>
                                        {{ __('Sign in') }}
                                    </a>
                                    <span class="text-muted small d-inline-flex align-items-center">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        {{ __('No credit card. Ready in minutes.') }}
                                    </span>
                                </div>
                                <div class="hero-pills">
                                    <span class="hero-pill">{{ __('Kickoff playbooks') }}</span>
                                    <span class="hero-pill">{{ __('Requirements workspace') }}</span>
                                    <span class="hero-pill">{{ __('Drive documents') }}</span>
                                    <span class="hero-pill">{{ __('Customer + contacts in one view') }}</span>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-4 mt-lg-0">
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <div class="metric-card">
                                            <div class="metric-title">
                                                <span>{{ __('Active clients') }}</span>
                                                <span class="metric-delta">
                                                    <i class="fas fa-arrow-up"></i>
                                                    12%
                                                </span>
                                            </div>
                                            <div class="metric-value" data-countup="18">18</div>
                                            <div class="text-muted small">
                                                {{ __('Engaged accounts this quarter') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <div class="metric-card">
                                            <div class="metric-title">
                                                <span>{{ __('Live projects') }}</span>
                                                <span class="metric-delta">
                                                    <i class="fas fa-check"></i>
                                                    96%
                                                </span>
                                            </div>
                                            <div class="metric-value" data-countup="12">12</div>
                                            <div class="text-muted small">
                                                {{ __('On-track milestones this month') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card mt-2">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div>
                                                        <span class="badge badge-info mr-2">{{ __('Today') }}</span>
                                                        <span class="font-weight-bold">{{ __('Kickoff call readiness') }}</span>
                                                    </div>
                                                    <span class="text-muted small">{{ __('Shared workspace') }}</span>
                                                </div>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-flex align-items-center mb-2">
                                                        <span class="badge badge-success mr-2"><i class="fas fa-check"></i></span>
                                                        <span class="text-muted">{{ __('Stakeholders confirmed and invited.') }}</span>
                                                    </li>
                                                    <li class="d-flex align-items-center mb-2">
                                                        <span class="badge badge-success mr-2"><i class="fas fa-check"></i></span>
                                                        <span class="text-muted">{{ __('Requirements collection room is live.') }}</span>
                                                    </li>
                                                    <li class="d-flex align-items-center">
                                                        <span class="badge badge-warning mr-2"><i class="fas fa-clock"></i></span>
                                                        <span class="text-muted">{{ __('Approval package ready for review.') }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                                <h5 class="mb-2">{{ __('Kickoff calls, simplified') }}</h5>
                                <p class="mb-0 text-muted">{{ __('Prep, schedule, and capture outcomes in one space with stakeholders already visible.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <h5 class="mb-2">{{ __('Requirements room') }}</h5>
                                <p class="mb-0 text-muted">{{ __('Capture, prioritize, and export ready-to-share requirements without switching tabs.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h5 class="mb-2">{{ __('Drive documents, organized') }}</h5>
                                <p class="mb-0 text-muted">{{ __('Versioned folders, rename, move, copy, and approvals with calm, consistent UX.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <h5 class="mb-2">{{ __('Customers + contacts together') }}</h5>
                                <p class="mb-0 text-muted">{{ __('Keep customer context, contacts, and linked projects in one view—no page reloads.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="main-footer text-center">
            <strong>{{ config('branding.name', config('app.name', 'Laravel')) }}</strong>
            <span class="text-muted">&copy; {{ now()->year }}</span>
        </footer>
    </div>
@endsection

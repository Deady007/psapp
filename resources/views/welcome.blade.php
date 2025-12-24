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
            <div class="content">
                <div class="container py-5">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h1 class="display-4 font-weight-bold">
                                {{ __('Plan, track, and deliver with confidence.') }}
                            </h1>
                            <p class="lead text-muted">
                                {{ __('Give your team a premium workspace for customers, projects, and execution clarity.') }}
                            </p>
                            <div class="mt-4">
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg mr-2">{{ __('Start free') }}</a>
                                @endif
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">{{ __('Sign in') }}</a>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-4 mt-lg-0">
                            <div class="card card-outline card-primary">
                                <div class="card-body">
                                    <h5 class="card-title d-block mb-3">{{ __('Workspace Snapshot') }}</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="info-box bg-info">
                                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">{{ __('Active Clients') }}</span>
                                                    <span class="info-box-number">18</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon"><i class="fas fa-briefcase"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">{{ __('Live Projects') }}</span>
                                                    <span class="info-box-number">12</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="callout callout-info mb-0">
                                                <h6 class="mb-1">{{ __('Delivery Pulse') }}</h6>
                                                <p class="mb-0 text-muted">{{ __('Milestones hitting forecast and approvals moving faster.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="text-primary mb-3"><i class="fas fa-layer-group fa-2x"></i></div>
                                    <h5>{{ __('Portfolio clarity') }}</h5>
                                    <p class="text-muted mb-0">{{ __('Group programs, surface dependencies, and keep context visible.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="text-primary mb-3"><i class="fas fa-bolt fa-2x"></i></div>
                                    <h5>{{ __('Momentum tracking') }}</h5>
                                    <p class="text-muted mb-0">{{ __('Spot blockers early with health scoring and smart reminders.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="text-primary mb-3"><i class="fas fa-file-alt fa-2x"></i></div>
                                    <h5>{{ __('Client-ready reports') }}</h5>
                                    <p class="text-muted mb-0">{{ __('Publish stakeholder-ready briefings in seconds.') }}</p>
                                </div>
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

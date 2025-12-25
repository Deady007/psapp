@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )
@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )

@if (config('adminlte.use_route_url', false))
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
    @php( $login_url = $login_url ? route($login_url) : '' )
@else
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
    @php( $login_url = $login_url ? url($login_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.password_reset_message'))
@section('auth_description', 'Set a new password to secure your account.')

@section('auth_body')
    <form action="{{ $password_reset_url }}" method="post" class="terminal-auth-form">
        @csrf

        {{-- Token field --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email field --}}
        <div class="input-group mb-3 terminal-auth-field">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus
                   autocomplete="email" inputmode="email">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3 terminal-auth-field">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.password') }}" autocomplete="new-password">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password confirmation field --}}
        <div class="input-group mb-3 terminal-auth-field">
            <input type="password" name="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   placeholder="{{ trans('adminlte::adminlte.retype_password') }}" autocomplete="new-password">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm password reset button --}}
        <button type="submit" class="btn btn-block terminal-btn terminal-auth-submit {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-sync-alt"></span>
            {{ __('adminlte::adminlte.reset_password') }}
        </button>

    </form>
@stop

@section('auth_footer')
    @if($login_url)
        <div class="terminal-auth-links">
            <p class="my-0">
                <a href="{{ $login_url }}">Back to Sign In</a>
            </p>
        </div>
    @endif
@stop

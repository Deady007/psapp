@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.password_reset_message'))

@section('auth_body')
    <form action="{{ $password_reset_url }}" method="post">
        @csrf

        {{-- Token field --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email field --}}
        <div class="form-group">
            <label for="reset_email">{{ __('adminlte::adminlte.email') }}</label>
            <div class="input-group">
                <input id="reset_email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus aria-describedby="reset-email-help">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
            </div>
            <small id="reset-email-help" class="form-text text-muted">{{ __('Enter the email you used to sign up.') }}</small>
            @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="form-group">
            <label for="reset_password">{{ __('adminlte::adminlte.password') }}</label>
            <div class="input-group">
                <input id="reset_password" type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="{{ __('adminlte::adminlte.password') }}" aria-describedby="reset-password-help">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
            </div>
            <small id="reset-password-help" class="form-text text-muted">{{ __('Pick a strong password you will remember.') }}</small>
            @error('password')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password confirmation field --}}
        <div class="form-group">
            <label for="reset_password_confirmation">{{ __('adminlte::adminlte.retype_password') }}</label>
            <div class="input-group">
                <input id="reset_password_confirmation" type="password" name="password_confirmation"
                       class="form-control @error('password_confirmation') is-invalid @enderror"
                       placeholder="{{ trans('adminlte::adminlte.retype_password') }}" aria-describedby="reset-password-confirmation-help">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                    </div>
                </div>
            </div>
            <small id="reset-password-confirmation-help" class="form-text text-muted">{{ __('Re-enter to confirm the new password.') }}</small>
            @error('password_confirmation')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm password reset button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-sync-alt"></span>
            {{ __('adminlte::adminlte.reset_password') }}
        </button>

    </form>
@stop

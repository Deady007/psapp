@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', __('adminlte::adminlte.verify_message'))

@section('auth_body')

    @if(session('resent'))
        <div class="alert alert-success" role="alert">
            {{ __('adminlte::adminlte.verify_email_sent') }}
        </div>
    @endif

    <p class="text-muted mb-3">
        {{ __('adminlte::adminlte.verify_check_your_email') }}
        {{ __('adminlte::adminlte.verify_if_not_recieved') }}
    </p>

    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-outline-primary">
            {{ __('adminlte::adminlte.verify_request_another') }}
        </button>
    </form>

@stop

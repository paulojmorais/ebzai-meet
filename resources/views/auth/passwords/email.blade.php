@extends('layouts.app')

@section('page', __('Reset Password'))
@section('title', getSetting('APPLICATION_NAME') . ' | ' . __('Reset Password'))

@section('content')
    <section class="auth-section">
        <div class="container auth-container">
            <div class="row align-items-center h-100">
                <div class="col-md-1"></div>
                <div class="col-12 col-md-10 main-authsection h-100">
                    <div class="row h-100 table-display">
                        <div class="col-12 col-md-6 h-100 auth-text-section">
                            <div class="text-center">
                                <div class="white-text">
                                    <h3>{{ __('Forgot your password?') }}</h3>
                                    {{ __('Already have the password?') }}<br /><a href="{{ route('login') }}"
                                        class="white-text"><u>{{ __('Login') }}</u></a>
                                </div>        
                            </div>
                            <div class="bg-set"></div>
                        </div>
                        <div class="col-12 col-md-6 auth-enterdata h-100">
                            <div class="card auth-info">
                                <div class="card-header text-center">
                                    {{ __('Reset Password') }}
                                </div>
                                <div class="card-body">
                                    @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf

                                        <div class="form-group row">
                                            <div class="col-12">
                                                <input id="email" placeholder="{{ __('E-Mail Address') }}" type="email"
                                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                                    value="{{ old('email') }}" maxlength="50" required
                                                    autocomplete="email" autofocus>

                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row mb-0">
                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ __('Send Password Reset Link') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

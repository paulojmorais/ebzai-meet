@extends('layouts.app')

@section('title', getSetting('APPLICATION_NAME') . ' | ' . $page)
@section('style')
@endsection
@section('content')

    <section class="auth-section">
        <div class="container auth-container">
            <div class="row align-items-center h-100">
                <div class="col-md-1"></div>
                <div class="col-12 col-lg-10 main-authsection h-100">
                    <div class="row h-100 table-display">
                        <div class="col-12 col-md-6 h-100 auth-text-section">
                            <div class="text-center">
                                <div class="white-text">
                                    <h3 class="white-text">{{ __('Welcome back!') }}</h3>
                                    @if (Route::has('register'))
                                        {{ __('Don\'t have an account yet?') }}<br /><a href="{{ route('register') }}"
                                            class="white-text"><u>{{ __('Register') }}</u></a>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-set"></div>
                        </div>
                        <div class="col-12 col-md-6 auth-enterdata h-100">
                            <div class="card auth-info mb-0">
                                <div class="card-header text-center">
                                    {{ __('Login') }}
                                    @if (getSetting('GOOGLE_SOCIAL_LOGIN') == 'enabled' ||
                                            getSetting('FACEBOOK_SOCIAL_LOGIN') == 'enabled' ||
                                            getSetting('LINKEDIN_SOCIAL_LOGIN') == 'enabled' ||
                                            getSetting('TWITTER_SOCIAL_LOGIN') == 'enabled')
                                        <p class="mb-0 small">With social media</p>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if (session('verify'))
                                        <div class="alert alert-warning" role="alert">
                                            {{ session('verify') }}
                                        </div>
                                    @endif
                                    <form id="login" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        @if (getSetting('GOOGLE_SOCIAL_LOGIN') == 'enabled' ||
                                                getSetting('FACEBOOK_SOCIAL_LOGIN') == 'enabled' ||
                                                getSetting('LINKEDIN_SOCIAL_LOGIN') == 'enabled' ||
                                                getSetting('TWITTER_SOCIAL_LOGIN') == 'enabled')
                                            <div class="form-group">
                                                <div class="col-md-12 social-login-links">
                                                    @if (getSetting('GOOGLE_SOCIAL_LOGIN') == 'enabled')
                                                        <a href="{{ route('login.google') }}" class="google btn">
                                                            <i class="fab fa-google"></i>
                                                        </a>
                                                    @endif
                                                    @if (getSetting('FACEBOOK_SOCIAL_LOGIN') == 'enabled')
                                                        <a href="{{ route('login.facebook') }}" class="fb btn">
                                                            <i class="fab fa-facebook-f"></i>
                                                        </a>
                                                    @endif
                                                    @if (getSetting('LINKEDIN_SOCIAL_LOGIN') == 'enabled')
                                                        <a href="{{ route('login.linkedin') }}" class="linkedin btn">
                                                            <i class="fab fa-linkedin-in"></i>
                                                        </a>
                                                    @endif
                                                    @if (getSetting('TWITTER_SOCIAL_LOGIN') == 'enabled')
                                                        <a href="{{ route('login.twitter') }}" class="twitter btn">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                                height="18" viewBox="0 0 512 512" id="twitter">
                                                                <g clip-path="url(#clip0_84_15698)">
                                                                    <path fill="#fff"
                                                                        d="M355.904 100H408.832L293.2 232.16L429.232 412H322.72L239.296 302.928L143.84 412H90.8805L214.56 270.64L84.0645 100H193.28L268.688 199.696L355.904 100ZM337.328 380.32H366.656L177.344 130.016H145.872L337.328 380.32Z">
                                                                    </path>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_84_15698">
                                                                        <rect width="512" height="512" fill="#fff">
                                                                        </rect>
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                </div>
                                                <hr>
                                            </div>
                                        @endif
                                        <div class="form-group">
                                            <div class="col-12">
                                                <input id="email" type="text"
                                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                                    value="{{ old('email') }}"
                                                    placeholder="{{ __('E-Mail Address or Username') }}" maxlength="50"
                                                    required autocomplete="email" autofocus>

                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-12">
                                                <input id="password" type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="{{ __('Password') }}" name="password" maxlength="50"
                                                    required autocomplete="current-password">

                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        @if (getSetting('CAPTCHA_LOGIN_PAGE') == 'enabled')
                                            <div class="form-group">
                                                <div class="col-12">
                                                    <div class="g-recaptcha" id="recaptcha-div"
                                                        data-sitekey="{{ getSetting('GOOGLE_RECAPTCHA_KEY') }}">
                                                    </div>
                                                    @if ($errors->has('g-recaptcha-response'))
                                                        <span class="invalid-feedback" role="alert"
                                                            style="display: block">
                                                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remember"
                                                        id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="remember">
                                                        {{ __('Remember Me') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-0">
                                            <div class="col-12 text-center">
                                                <button id="loginButton" type="submit" class="btn btn-primary mb-2">
                                                    {{ __('Login') }}
                                                </button>

                                                @if (Route::has('password.request'))
                                                    <p class="mb-0">
                                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                                            {{ __('Forgot Your Password') }}
                                                        </a>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                @if (isDemoMode())
                                    <div>
                                        <select id="autoLogin" class="form-control">
                                            <option value="">Auto login as (demo only)</option>
                                            <option value="admin">Admin</option>
                                            <option value="user_1">User 1</option>
                                            <option value="user_2">User 2</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script src="https://www.google.com/recaptcha/api.js"></script>
@endsection

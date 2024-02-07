@extends('layouts.app')

@section('content')
    <section class="py-3 py-md-5">
        <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
            <div class="card border border-light-subtle rounded-3 shadow-sm">
                <div class="card-body p-3 p-md-4 p-xl-5">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-4 text-center">
                                <h3>Real-time chat</h3>
                            </div>
                        </div>
                    </div>
                <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Sign in to your account</h2>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="row gy-2 overflow-hidden">
                    <div class="col-12">
                        <div class="form-floating mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="name@example.com" autocomplete="email" autofocus>
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required autocomplete="current-password" placeholder="Password" required>
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-between">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-secondary" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-primary text-decoration-none">{{ __('Forgot Your Password?') }}</a>
                        @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-grid my-3">
                        <button class="btn btn-primary btn-lg" type="submit">{{ __('Login') }}</button>
                        </div>
                    </div>
                    <div class="col-12">
                        <p class="m-0 text-secondary text-center">Don't have an account? <a href="{{ route('register') }}" class="link-primary text-decoration-none">Sign up</a></p>
                    </div>
                    </div>
                </form>
                </div>
            </div>
            </div>
        </div>
        </div>
    </section>
@endsection
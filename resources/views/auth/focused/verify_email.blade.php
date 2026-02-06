@extends('auth.layouts.authentication')

@section('content')
    <!-- aiz-main-wrapper -->
    <div class="aiz-main-wrapper d-flex flex-column justify-content-md-center bg-white">
        <section class="bg-white overflow-hidden">
            <div class="row">
                <div class="col-xxl-6 col-xl-9 col-lg-10 col-md-7 mx-auto py-lg-4">
                    <div class="card shadow-none rounded-0 border-0">
                        <div class="row no-gutters">
                            <!-- Left Side Image-->
                            <div class="col-lg-6">
                                <img src="{{ uploaded_asset(get_setting('password_reset_page_image')) }}"
                                     alt="{{ translate('Password Reset Page Image') }}" class="img-fit h-100">
                            </div>

                            <div class="col-lg-6 p-4 p-lg-5 d-flex flex-column justify-content-center border right-content"
                                 style="height: auto;">
                                <!-- Site Icon -->
                                <div class="size-48px mb-3 mx-auto mx-lg-0">
                                    <img src="{{ uploaded_asset(get_setting('site_icon')) }}"
                                         alt="{{ translate('Site Icon')}}" class="img-fit h-100">
                                </div>

                                <!-- Titles -->
                                <div class="text-center text-lg-left">
                                    <h1 class="fs-20 fs-md-24 fw-700 text-primary"
                                        style="text-transform: uppercase;">{{ translate('Verify Your Email Address') }}</h1>
                                    <p class="fs-14 fw-400 text-dark mt-2">
                                        {{ translate('Before proceeding, please check your email for a verification link.') }}
                                    </p>
                                    <p class="fs-14 fw-400 text-dark">
                                        {{ translate('If you did not receive the email, click the button below to request another.') }}
                                    </p>
                                </div>

                                <!-- Resend form -->
                                <div class="pt-3">
                                    <form method="POST" action="{{ route('verification.resend') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="email"
                                                   class="fs-12 fw-700 text-soft-dark">{{ translate('Email Address') }}</label>
                                            <input type="email"
                                                   class="form-control rounded-0{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                   id="email"
                                                   name="email"
                                                   value="{{ old('email', auth()->user()->email ?? '') }}"
                                                   placeholder="{{ translate('your@email.com') }}"
                                                   required>
                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-block fw-700">
                                            {{ translate('Resend Verification Email') }}
                                        </button>
                                    </form>
                                </div>

                                <!-- Back to Login -->
                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}" class="fs-14 fw-400 text-primary">
                                        {{ translate('Back to Login') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@extends('layouts.master')

@section('title', __('customer/password_reset_page.reset_title'))

@section('content')

    <!-- password reset section start -->
    <section class="log-in-section section-b-space forgot-section">
        <div class="container-fluid-lg w-100">
            <div class="row">
                <div class="col-xxl-6 col-xl-5 col-lg-6 d-lg-block d-none ms-auto">
                    <div class="image-contain">
                        <img src="{{ asset('assets/images/inner-page/forgot.png') }}" class="img-fluid" alt="">
                    </div>
                </div>

                <div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-8 mx-auto">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="log-in-box">
                            <div class="log-in-title">
                                <h3>{{ __('customer/password_reset_page.welcome') }}</h3>
                                <h4>{{ __('customer/password_reset_page.create_new_password') }}</h4>
                            </div>

                            <div class="input-box">
                                <form class="row g-4" action="{{ route('password.update') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <div class="col-12">
                                        <div class="form-floating theme-form-floating log-in-form">
                                            <input type="email" class="form-control" id="email" name="email"
                                                   value="{{ $email ?? old('email') }}"
                                                   placeholder="{{ __('customer/password_reset_page.email_address') }}"
                                                   required autofocus>
                                            <label for="email">{{ __('customer/password_reset_page.email_address') }}</label>
                                            @error('email')
                                            <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating theme-form-floating log-in-form">
                                            <input type="password" class="form-control" id="password" name="password"
                                                   placeholder="{{ __('customer/password_reset_page.new_password') }}"
                                                   required>
                                            <label for="password">{{ __('customer/password_reset_page.new_password') }}</label>
                                            @error('password')
                                            <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating theme-form-floating log-in-form">
                                            <input type="password" class="form-control" id="password-confirm"
                                                   name="password_confirmation"
                                                   placeholder="{{ __('customer/password_reset_page.confirm_password') }}"
                                                   required>
                                            <label for="password-confirm">{{ __('customer/password_reset_page.confirm_password') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-animation w-100"
                                                type="submit">{{ __('customer/password_reset_page.reset_password_button') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- password reset section end -->

@endsection

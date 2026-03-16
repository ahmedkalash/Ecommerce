@extends('layouts.master')

@section('title', __('customer/login_page.title'))

@section('content')
    <!-- log in section start -->
    <section class="log-in-section background-image-2 section-b-space">
        <div class="container-fluid-lg w-100">
            <div class="row">
                <div class="col-xxl-6 col-xl-5 col-lg-6 d-lg-block d-none ms-auto">
                    <div class="image-contain">
                        <img src="{{asset('assets/images/inner-page/log-in.png')}}" class="img-fluid" alt="">
                    </div>
                </div>

                <div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-8 mx-auto">
                    <div class="log-in-box">
                        <div class="log-in-title">
                            <h3>{{ __('customer/login_page.welcome') }}</h3>
                            <h4>{{ __('customer/login_page.log_in_your_account') }}</h4>
                        </div>

                        <div class="input-box">
                            @livewire('auth.login')
                        </div>

                        <div class="other-log-in">
                            <h6>{{ __('customer/login_page.or') }}</h6>
                        </div>

                        @include('partials.auth.social_login')

                        <div class="other-log-in">
                            <h6></h6>
                        </div>

                        <div class="sign-up-box">
                            <h4>{{ __('customer/login_page.dont_have_account') }}</h4>
                            <a href="{{route('user.registration')}}">{{ __('customer/login_page.sign_up') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- log in section end -->

@endsection

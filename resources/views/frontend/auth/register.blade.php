@extends('layouts.master')

@section('title', __('customer/register_page.title'))

@section('content')
    <!-- Register section start -->
    <section class="log-in-section section-b-space">
        <div class="container-fluid-lg w-100">
            <div class="row">
                <div class="col-xxl-6 col-xl-5 col-lg-6 d-lg-block d-none ms-auto">
                    <div class="image-contain">
                        <img src="{{asset('assets/images/inner-page/sign-up.png')}}" class="img-fluid" alt="">
                    </div>
                </div>

                <div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-8 mx-auto">
                    <div class="log-in-box">
                        <div class="log-in-title">
                            <h3>{{ __('customer/register_page.welcome') }}</h3>
                            <h4>{{ __('customer/register_page.create_account') }}</h4>
                        </div>

                        <div class="input-box">
                            @livewire('auth.register')
                        </div>

                        <div class="other-log-in">
                            <h6>{{ __('customer/register_page.or') }}</h6>
                        </div>

                        @include('partials.auth.social_login')

                        <div class="other-log-in">
                            <h6></h6>
                        </div>

                        <div class="sign-up-box">
                            <h4>{{ __('customer/register_page.already_have_account') }}</h4>
                            <a href="{{route('user.login')}}">{{ __('customer/register_page.log_in') }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-7 col-xl-6 col-lg-6"></div>
            </div>
        </div>
    </section>
    <!-- Register section end -->
@endsection

@extends('layouts.master')

@section('title', __('customer/404_page.title'))

@section('content')
    <!-- 404 Section Start -->
    <section class="section-404 section-lg-space">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="image-404">
                        <img src="{{asset('assets/images/inner-page/404.png')}}" class="img-fluid blur-up lazyload"
                             alt="">
                    </div>
                </div>

                <div class="col-12">
                    <div class="contain-404">
                        <h3 class="text-content">
                            {{__('customer/404_page.not_found')}}
                        </h3>
                        <button onclick="location.href = '{{route('home')}}';"
                                class="btn btn-md text-white theme-bg-color mt-4 mx-auto">
                            {{__('customer/404_page.back_to_home')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- 404 Section End -->
@endsection
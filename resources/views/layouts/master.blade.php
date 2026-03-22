<!DOCTYPE html>
@php
    use App\Services\LocaleService;
@endphp

<html lang="{{LocaleService::getCurrentLocaleLangCode()}}"
      dir="{{LocaleService::getCurrentLocaleDir()}}">

@php
    $isRtl = LocaleService::getCurrentLocaleDir() === 'rtl';
@endphp

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content=""> {{--todo: handle seo and meta info--}}
    <meta name="keywords" content=""> {{--todo: handle seo and meta info--}}
    <meta name="author" content=""> {{--todo: handle seo and meta info--}}
    @yield('meta')

    <link rel="icon" href="{{ asset("storage\\$site_icon") }}" type="image/x-icon">

    <title>
        @yield('title')
    </title>

    <!-- Google font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700;800;900&display=swap"
          rel="stylesheet">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">

    <!-- bootstrap css -->
    <link id="rtl-link" rel="stylesheet" type="text/css"
          href="{{ $isRtl ? asset('assets/css/vendors/bootstrap.rtl.css') : asset('assets/css/vendors/bootstrap.css') }}">

    <!-- wow css -->
    <link rel="stylesheet" href="{{asset('assets/css/animate.min.css')}}">

    <!-- Iconly css -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/bulk-style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/animate.css')}}">

    <!-- ion.rangeSlider css -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/ion.rangeSlider.min.css')}}">

    <!-- Template css -->
    <link id="color-link" rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
    @yield('head_css')
    @yield('head_js')

    <!-- Theme Mode Script (Prevents FOUC) -->
    <script>
        (function () {
            var theme = localStorage.getItem('theme-mode');
            if (theme === 'dark') {
                document.getElementById('color-link').setAttribute('href', '{{asset('assets/css/dark.css')}}');
            }
        })();
    </script>

    {{-- Header right-side-menu styles (SCSS was never compiled, so we replicate the rules here) --}}
    <style>
        .rightside-box {
            display: flex;
            align-items: center;
        }

        .right-side-menu {
            display: flex;
            align-items: center;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }

        .right-side-menu .right-side {
            position: relative;
            padding-right: 32px;
        }

        .right-side-menu .right-side:last-child {
            padding-right: 0;
        }

        .right-side-menu .right-side:last-child::before {
            content: none;
        }

        .right-side-menu .right-side::before {
            content: "";
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 24px;
            right: 14px;
            background-color: rgba(119, 119, 119, 0.5);
        }

        .right-side-menu .right-side .delivery-login-box {
            display: flex;
            align-items: center;
            cursor: default;
        }

        .right-side-menu .right-side .delivery-login-box .delivery-icon .feather {
            margin-right: 14px;
            stroke-width: 1.5;
        }

        .right-side-menu .right-side .delivery-login-box .delivery-detail h6 {
            margin-bottom: 3px;
        }

        .right-side-menu .right-side .delivery-login-box .delivery-detail h5 {
            font-weight: 500;
        }

        .right-side-menu .right-side .header-badge {
            padding-right: 9px;
        }

        .right-side-menu .right-side .header-wishlist .feather {
            stroke-width: 1.5;
        }

        .right-side-menu .right-side .header-wishlist:focus {
            box-shadow: none;
        }

        .right-side-menu .right-side .header-wishlist span {
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ff7272;
            font-size: 12px;
            padding: 0;
            border-radius: 2px;
        }

        /* RTL overrides for the right-side-menu */
        [dir="rtl"] .right-side-menu {
            padding-right: 0;
        }

        [dir="rtl"] .right-side-menu .right-side {
            padding-right: unset;
            padding-left: 32px;
        }

        [dir="rtl"] .right-side-menu .right-side:last-child {
            padding-left: 0;
        }

        [dir="rtl"] .right-side-menu .right-side::before {
            right: unset;
            left: 14px;
        }

        [dir="rtl"] .right-side-menu .right-side .delivery-login-box .delivery-icon .feather {
            margin-right: unset;
            margin-left: 14px;
        }

        [dir="rtl"] .right-side-menu .right-side .header-wishlist span {
            right: 0 !important;
        }
    </style>

    @if($isRtl)
        <!-- RTL specific style overrides -->
        <style>
            .breadcrumb-section .breadcrumb .breadcrumb-item + .breadcrumb-item::before {
                content: "\f104" !important;
                font-family: "Font Awesome 6 Free" !important;
                font-weight: 900 !important;
            }
        </style>
    @endif

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @livewireStyles
</head>

<body class="bg-effect {{ $isRtl ? 'rtl' : 'ltr' }}">
<script>
    if (localStorage.getItem('theme-mode') === 'dark') {
        document.body.classList.add('dark');
        document.body.classList.remove('light');
    }
</script>

<!-- Loader Start -->
<div class="fullpage-loader">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
</div>
<!-- Loader End -->

@include('partials.header')

<!-- mobile fix menu start -->
<div class="mobile-menu d-md-none d-block mobile-cart">
    <ul>
        <li class="active">
            <a href="index.html">
                <i class="iconly-Home icli"></i>
                <span>Home</span>
            </a>
        </li>

        <li class="mobile-category">
            <a href="javascript:void(0)">
                <i class="iconly-Category icli js-link"></i>
                <span>Category</span>
            </a>
        </li>

        <li>
            <a href="search.html" class="search-box">
                <i class="iconly-Search icli"></i>
                <span>Search</span>
            </a>
        </li>

        <li>
            <a href="wishlist.html" class="notifi-wishlist">
                <i class="iconly-Heart icli"></i>
                <span>My Wish</span>
            </a>
        </li>

        <li>
            <a href="cart.html">
                <i class="iconly-Bag-2 icli fly-cate"></i>
                <span>Cart</span>
            </a>
        </li>
    </ul>
</div>
<!-- mobile fix menu end -->

{{--
<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section pt-0">
    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-contain">
                    <h2>OTP</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="index.html">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">OTP</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

--}}

<div class="row">
    <div class="col-12">
        @include('flash::message')
    </div>
</div>

@yield('content')

@include('partials.footer')

<!-- Location Modal Start -->
<div class="modal location-modal fade theme-modal" id="locationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Choose your Delivery Location</h5>
                <p class="mt-1 text-content">Enter your address and we will specify the offer for your area.</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="location-list">
                    <div class="search-input">
                        <input type="search" class="form-control" placeholder="Search Your Area">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>

                    <div class="disabled-box">
                        <h6>Select a Location</h6>
                    </div>

                    <ul class="location-select custom-height">
                        <li>
                            <a href="javascript:void(0)">
                                <h6>Alabama</h6>
                                <span>Min: $130</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <h6>Arizona</h6>
                                <span>Min: $150</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <h6>California</h6>
                                <span>Min: $110</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <h6>Colorado</h6>
                                <span>Min: $140</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <h6>Florida</h6>
                                <span>Min: $160</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <h6>Georgia</h6>
                                <span>Min: $120</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <h6>Kansas</h6>
                                <span>Min: $170</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <h6>Minnesota</h6>
                                <span>Min: $120</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <h6>New York</h6>
                                <span>Min: $110</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)">
                                <h6>Washington</h6>
                                <span>Min: $130</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Location Modal End -->
{{--

<!-- Cookie Bar Box Start -->
<div class="cookie-bar-box">
    <div class="cookie-box">
        <div class="cookie-image">
            <img src="{{asset('assets/images/cookie-bar.png')}}" class="blur-up lazyload" alt="">
            <h2>Cookies!</h2>
        </div>

        <div class="cookie-contain">
            <h5 class="text-content">We use cookies to make your experience better</h5>
        </div>
    </div>

    <div class="button-group">
        <button class="btn privacy-button">Privacy Policy</button>
        <button class="btn ok-button">OK</button>
    </div>
</div>
<!-- Cookie Bar Box End -->
--}}

{{--
<!-- Deal Box Modal Start -->
<div class="modal fade theme-modal deal-modal" id="deal-box" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title w-100" id="deal_today">Deal Today</h5>
                    <p class="mt-1 text-content">Recommended deals for you.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="deal-offer-box">
                    <ul class="deal-offer-list">
                        <li class="list-1">
                            <div class="deal-offer-contain">
                                <a href="shop-left-sidebar.html" class="deal-image">
                                    <img src="{{asset('assets/images/vegetable/product/10.png')}}"
                                         class="blur-up lazyload"
                                         alt="">
                                </a>

                                <a href="shop-left-sidebar.html" class="deal-contain">
                                    <h5>Blended Instant Coffee 50 g Buy 1 Get 1 Free</h5>
                                    <h6>$52.57
                                        <del>57.62</del>
                                        <span>500 G</span></h6>
                                </a>
                            </div>
                        </li>

                        <li class="list-2">
                            <div class="deal-offer-contain">
                                <a href="shop-left-sidebar.html" class="deal-image">
                                    <img src="{{asset('assets/images/vegetable/product/11.png')}}"
                                         class="blur-up lazyload"
                                         alt="">
                                </a>

                                <a href="shop-left-sidebar.html" class="deal-contain">
                                    <h5>Blended Instant Coffee 50 g Buy 1 Get 1 Free</h5>
                                    <h6>$52.57
                                        <del>57.62</del>
                                        <span>500 G</span></h6>
                                </a>
                            </div>
                        </li>

                        <li class="list-3">
                            <div class="deal-offer-contain">
                                <a href="shop-left-sidebar.html" class="deal-image">
                                    <img src="{{asset('assets/images/vegetable/product/12.png')}}"
                                         class="blur-up lazyload"
                                         alt="">
                                </a>

                                <a href="shop-left-sidebar.html" class="deal-contain">
                                    <h5>Blended Instant Coffee 50 g Buy 1 Get 1 Free</h5>
                                    <h6>$52.57
                                        <del>57.62</del>
                                        <span>500 G</span></h6>
                                </a>
                            </div>
                        </li>

                        <li class="list-1">
                            <div class="deal-offer-contain">
                                <a href="shop-left-sidebar.html" class="deal-image">
                                    <img src="{{asset('assets/images/vegetable/product/13.png')}}"
                                         class="blur-up lazyload"
                                         alt="">
                                </a>

                                <a href="shop-left-sidebar.html" class="deal-contain">
                                    <h5>Blended Instant Coffee 50 g Buy 1 Get 1 Free</h5>
                                    <h6>$52.57
                                        <del>57.62</del>
                                        <span>500 G</span></h6>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Deal Box Modal End -->
--}}

<!-- Tap to top and theme setting button start -->

<div class="theme-option">
    <div class="setting-box">
        <button class="btn setting-button">
            <i class="fa-solid fa-gear"></i>
        </button>

        <div class="theme-setting-2">
            <div class="theme-box">
                <ul>
                    <li>
                        <div class="setting-name">
                            <h4>Color</h4>
                        </div>
                        <div class="theme-setting-button color-picker">
                            <form class="form-control">
                                <label for="colorPick" class="form-label mb-0">Theme Color</label>
                                <input type="color" class="form-control form-control-color" id="colorPick"
                                       value="#0da487" title="Choose your color">
                            </form>
                        </div>
                    </li>

                    <li>
                        <div class="setting-name">
                            <h4>Dark</h4>
                        </div>
                        <div class="theme-setting-button">
                            <button class="btn btn-2 outline" id="darkButton">Dark</button>
                            <button class="btn btn-2 unline" id="lightButton">Light</button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="back-to-top">
        <a id="back-to-top" href="#">
            <i class="fas fa-chevron-up"></i>
        </a>
    </div>
</div>
<!-- Tap to top and theme setting button end -->

<!-- Bg overlay Start -->
<div class="bg-overlay"></div>
<!-- Bg overlay End -->

@include('partials.footer_js')
{{-- SweetAlert2 JS (Required for livewire-alert) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@include('sweetalert::alert')
@livewireScripts
</body>

</html>
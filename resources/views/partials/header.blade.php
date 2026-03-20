@php use App\Services\LocaleService; @endphp

        <!-- Header Start -->
<header class="pb-md-4 pb-0">
    <div class="header-top">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-xxl-3 d-xxl-block d-none">
                    <div class="top-left-header">
                        <i class="iconly-Location icli text-white"></i>
                        <span class="text-white">{{ __('customer/header.store_address') }}</span>
                    </div>
                </div>

                <div class="col-xxl-6 col-lg-9 d-lg-block d-none">
                    <div class="header-offer">
                        <div class="notification-slider">
                            <div>
                                <div class="timer-notification">
                                    <h6>
                                        <strong class="me-1">{{ __('customer/header.welcome_text') }}</strong>
                                        {{ __('customer/header.welcome_sub_text') }}
                                        <strong class="ms-1">{{ __('customer/header.new_coupon_code') }} "Fast024"
                                        </strong>

                                    </h6>
                                </div>
                            </div>

                            <div>
                                <div class="timer-notification">
                                    <h6>{{ __('customer/header.sale_notification') }}
                                        <a href="shop-left-sidebar.html"
                                           class="text-white">{{ __('customer/header.buy_now') }}</a>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <ul class="about-list right-nav-about">
                        <li class="right-nav-list">
                            <div class="dropdown theme-form-select">
                                <button class="btn dropdown-toggle" type="button" id="select-language"
                                        data-bs-toggle="dropdown">
                                    @php
                                        $current_locale = LocaleService::getCurrentFullLocale();
                                        $current_locale_flag = LocaleService::getFullLocaleFlag($current_locale);
                                    @endphp
                                    <img src="{{$current_locale_flag}}" class="img-fluid blur-up lazyload"
                                         alt="{{$current_locale}}">
                                    <span>{{LocaleService::getCurrentLocaleName() }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @foreach(LocaleService::getSupportedLocales() as $locale => $info)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('language.switch', $locale) }}"
                                               id="english">
                                                <img src="{{LocaleService::getFullLocaleFlag($locale)}}"
                                                     class="img-fluid blur-up lazyload" alt="{{$info['name']}}">
                                                <span>{{ $info['name'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                        <li class="right-nav-list">
                            <div class="dropdown theme-form-select">
                                <button class="btn dropdown-toggle" type="button" id="select-dollar"
                                        data-bs-toggle="dropdown">
                                    <span>USD</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end sm-dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" id="aud" href="javascript:void(0)">AUD</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" id="eur" href="javascript:void(0)">EUR</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" id="cny" href="javascript:void(0)">CNY</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="top-nav top-header sticky-header">
        <div class="container-fluid-lg">
            <div class="row">
                <div class="col-12">
                    <div class="navbar-top">
                        <button class="navbar-toggler d-xl-none d-inline navbar-menu-button" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#primaryMenu">
                                    <span class="navbar-toggler-icon">
                                        <i class="fa-solid fa-bars"></i>
                                    </span>
                        </button>
                        <a href="{{route('home')}}" class="web-logo nav-logo">
                            <img src="{{ asset("storage\\$header_logo") }}" class="img-fluid blur-up lazyload"
                                 alt="">
                        </a>

                        <div class="middle-box">
                            <div class="location-box">
                                <button class="btn location-button" data-bs-toggle="modal"
                                        data-bs-target="#locationModal">
                                            <span class="location-arrow">
                                                <i data-feather="map-pin"></i>
                                            </span>
                                    <span class="locat-name">{{ __('customer/header.your_location') }}</span>
                                    <i class="fa-solid fa-angle-down"></i>
                                </button>
                            </div>

                            <!-- Header Search Livewire Component -->
                            <livewire:frontend.header-search type="desktop"/>
                        </div>

                        <!-- Mobile Header Search Livewire Component -->
                        <livewire:frontend.header-search type="mobile"/>
                        <ul class="right-side-menu">

                            <li class="right-side">
                                <a href="wishlist.html" class="btn p-0 position-relative header-wishlist">
                                    <i data-feather="heart"></i>
                                </a>
                            </li>
                            <li class="right-side">
                                <div class="onhover-dropdown header-badge">
                                    <button type="button" class="btn p-0 position-relative header-wishlist">
                                        <i data-feather="shopping-cart"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge">2
                                                        <span class="visually-hidden">unread messages</span>
                                                    </span>
                                    </button>

                                    <div class="onhover-div">
                                        <ul class="cart-list">
                                            <li class="product-box-contain">
                                                <div class="drop-cart">
                                                    <a href="product-left-thumbnail.html" class="drop-image">
                                                        <img src="{{asset('assets/images/vegetable/product/1.png')}}"
                                                             class="blur-up lazyload" alt="">
                                                    </a>

                                                    <div class="drop-contain">
                                                        <a href="product-left-thumbnail.html">
                                                            <h5>Fantasy Crunchy Choco Chip Cookies</h5>
                                                        </a>
                                                        <h6><span>1 x</span> $80.58</h6>
                                                        <button class="close-button close_button">
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>

                                            <li class="product-box-contain">
                                                <div class="drop-cart">
                                                    <a href="product-left-thumbnail.html" class="drop-image">
                                                        <img src="{{asset('assets/images/vegetable/product/2.png')}}"
                                                             class="blur-up lazyload" alt="">
                                                    </a>

                                                    <div class="drop-contain">
                                                        <a href="product-left-thumbnail.html">
                                                            <h5>Peanut Butter Bite Premium Butter Cookies 600 g
                                                            </h5>
                                                        </a>
                                                        <h6><span>1 x</span> $25.68</h6>
                                                        <button class="close-button close_button">
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>

                                        <div class="price-box">
                                            <h5>{{ __('customer/header.total') }}</h5>
                                            <h4 class="theme-color fw-bold">$106.58</h4>
                                        </div>

                                        <div class="button-group">
                                            <a href="cart.html"
                                               class="btn btn-sm cart-button">{{ __('customer/header.view_cart') }}</a>
                                            <a href="checkout.html" class="btn btn-sm cart-button theme-bg-color
                                                        text-white">{{ __('customer/header.checkout') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="right-side onhover-dropdown">
                                <div class="delivery-login-box">
                                    <div class="delivery-icon">
                                        <i data-feather="user"></i>
                                    </div>
                                    <div class="delivery-detail">
                                        @auth
                                            <h6>{{ __('customer/header.hello') }} ,{{ Auth::user()->name }}</h6>
                                            <h5>{{ __('customer/header.my_account') }}</h5>
                                        @else
                                            <h6>{{ __('customer/header.log_in') }}
                                                / {{ __('customer/header.register') }}</h6>
                                        @endauth

                                    </div>
                                </div>

                                <div class="onhover-div onhover-div-login">
                                    <ul class="user-box-name">
                                        @auth
                                            <li class="product-box-contain">
                                                <i></i>
                                                <a href="#">{{ __('customer/header.dashboard')}}</a>
                                            </li>
                                            <li class="product-box-contain">
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <a href="#"
                                                       onclick="event.preventDefault(); this.closest('form').submit();">
                                                        {{ __('customer/header.logout')}}
                                                    </a>
                                                </form>
                                            </li>
                                        @else
                                            <li class="product-box-contain">
                                                <i></i>
                                                <a href="{{ route('user.login') }}">{{ __('customer/header.log_in') }}</a>
                                            </li>

                                            <li class="product-box-contain">
                                                <a href="{{ route('user.registration') }}">{{ __('customer/header.register') }}</a>
                                            </li>
                                            <li class="product-box-contain">
                                                <a href="{{ route('password.request') }}">{{ __('customer/header.forgot_password') }}</a>
                                            </li>
                                        @endauth
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="container-fluid-lg">
        <div class="row">
            <div class="col-12">
                <div class="header-nav">
                    <div class="header-nav-left">
                        <button class="dropdown-category">
                            <i data-feather="align-left"></i>
                            <span>{{ __('customer/header.all_categories') }}</span>
                        </button>

                        <div class="category-dropdown">
                            <div class="category-title">
                                <h5>{{ __('customer/header.categories') }}</h5>
                                <button type="button" class="btn p-0 close-button text-content">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <ul class="category-list">
                                <li class="onhover-category-list">
                                    <a href="javascript:void(0)" class="category-name">
                                        <img src="{{asset('assets/svg/1/vegetable.svg')}}" alt="">
                                        <h6>{{ __('customer/header.vegetables_fruit') }}</h6>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>

                                    <div class="onhover-category-box">
                                        <div class="list-1">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.organic_vegetables') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.potato_tomato') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.cucumber_capsicum') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.leafy_vegetables') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.root_vegetables') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.beans_okra') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.cabbage_cauliflower') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.gourd_drumstick') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.specialty') }}</a>
                                                </li>
                                            </ul>
                                            <div class="category-title-box">
                                                <h5>Organic Vegetables</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">Potato & Tomato</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">Cucumber & Capsicum</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">Leafy Vegetables</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">Root Vegetables</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">Beans & Okra</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">Cabbage & Cauliflower</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">Gourd & Drumstick</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">Specialty</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>

                                <li class="onhover-category-list">
                                    <a href="javascript:void(0)" class="category-name">
                                        <img src="{{asset('assets/svg/1/cup.svg')}}" alt="">
                                        <h6>{{ __('customer/header.beverages') }}</h6>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>

                                    <div class="onhover-category-box w-100">
                                        <div class="list-1">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.energy_soft_drinks') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.soda_cocktail_mix') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.soda_cocktail_mix') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.sports_energy_drinks') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.non_alcoholic_drinks') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.packaged_water') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.spring_water') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.flavoured_water') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>

                                <li class="onhover-category-list">
                                    <a href="javascript:void(0)" class="category-name">
                                        <img src="{{asset('assets/svg/1/meats.svg')}}" alt="">
                                        <h6>Meats & Seafood</h6>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>

                                    <div class="onhover-category-box">
                                        <div class="list-1">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.meat') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.fresh_meat') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.frozen_meat') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.marinated_meat') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.fresh_frozen_meat') }}</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="list-2">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.seafood') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.fresh_water_fish') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.dry_fish') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.frozen_fish_seafood') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.marine_water_fish') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.canned_seafood') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.prawns_shrimps') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.other_seafood') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>

                                <li class="onhover-category-list">
                                    <a href="javascript:void(0)" class="category-name">
                                        <img src="{{asset('assets/svg/1/breakfast.svg')}}" alt="">
                                        <h6>{{ __('customer/header.breakfast_dairy') }}</h6>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>

                                    <div class="onhover-category-box">
                                        <div class="list-1">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.breakfast_cereals') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.oats_porridge') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.kids_cereal') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.muesli') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.flakes') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.granola_cereal_bars') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.instant_noodles') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.pasta_macaroni') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.frozen_non_veg_snacks') }}</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="list-2">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.dairy') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.milk') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.curd') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.paneer_tofu_cream') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.butter_margarine') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.condensed_powdered_milk') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.buttermilk_lassi') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.yogurt_shrikhand') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.flavoured_soya_milk') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>

                                <li class="onhover-category-list">
                                    <a href="javascript:void(0)" class="category-name">
                                        <img src="{{asset('assets/svg/1/frozen.svg')}}" alt="">
                                        <h6>{{ __('customer/header.frozen_foods') }}</h6>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>

                                    <div class="onhover-category-box w-100">
                                        <div class="list-1">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.noodle_pasta') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.instant_noodles') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.hakka_noodles') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.cup_noodles') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.vermicelli') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.instant_pasta') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>

                                <li class="onhover-category-list">
                                    <a href="javascript:void(0)" class="category-name">
                                        <img src="{{asset('assets/svg/1/biscuit.svg')}}" alt="">
                                        <h6>{{ __('customer/header.biscuits_snacks') }}</h6>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>

                                    <div class="onhover-category-box">
                                        <div class="list-1">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.biscuits_cookies') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.salted_biscuits') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.marie_health_digestive') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.cream_biscuits_wafers') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.glucose_milk_biscuits') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.cookies') }}</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="list-2">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.bakery_snacks') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.bread_sticks_lavash') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.cheese_garlic_bread') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.puffs_patties_sandwiches') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.breadcrumbs_croutons') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>

                                <li class="onhover-category-list">
                                    <a href="javascript:void(0)" class="category-name">
                                        <img src="{{asset('assets/svg/1/grocery.svg')}}" alt="">
                                        <h6>{{ __('customer/header.grocery_staples') }}</h6>
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>

                                    <div class="onhover-category-box">
                                        <div class="list-1">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.grocery') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.lemon_ginger_garlic') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.indian_exotic_herbs') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_vegetables') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_fruits') }}</a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="list-2">
                                            <div class="category-title-box">
                                                <h5>{{ __('customer/header.organic_staples') }}</h5>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_dry_fruits') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_dals_pulses') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_millet_flours') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_sugar_jaggery') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_masalas_spices') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_rice_other_rice') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_flours') }}</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)">{{ __('customer/header.organic_edible_oil_ghee') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="header-nav-middle">
                        <div class="main-nav navbar navbar-expand-xl navbar-light navbar-sticky">
                            <div class="offcanvas offcanvas-collapse order-xl-2" id="primaryMenu">
                                <div class="offcanvas-header navbar-shadow">
                                    <h5>{{ __('customer/header.nav_menu') }}</h5>
                                    <button class="btn-close lead" type="button"
                                            data-bs-dismiss="offcanvas"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <ul class="navbar-nav">
                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="javascript:void(0)"
                                               data-bs-toggle="dropdown">{{ __('customer/header.nav_home') }}</a>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index.html">{{ __('customer/header.nav_kartshop') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-2.html">{{ __('customer/header.nav_sweetshop') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-3.html">{{ __('customer/header.nav_organic') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-4.html">{{ __('customer/header.nav_supershop') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-5.html">{{ __('customer/header.nav_classic_shop') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-6.html">{{ __('customer/header.nav_furniture') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-7.html">{{ __('customer/header.nav_search_oriented') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-8.html">{{ __('customer/header.nav_category_focus') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-9.html">{{ __('customer/header.nav_fashion') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-10.html">{{ __('customer/header.nav_book') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="index-11.html">{{ __('customer/header.nav_digital') }}</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="javascript:void(0)"
                                               data-bs-toggle="dropdown">{{ __('customer/header.nav_shop') }}</a>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="shop-category-slider.html">{{ __('customer/header.nav_shop_category_slider') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="shop-category.html">{{ __('customer/header.nav_shop_category_sidebar') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="shop-banner.html">{{ __('customer/header.nav_shop_banner') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="shop-left-sidebar.html">{{ __('customer/header.nav_shop_left_sidebar') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="shop-list.html">{{ __('customer/header.nav_shop_list') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="shop-right-sidebar.html">{{ __('customer/header.nav_shop_right_sidebar') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="shop-top-filter.html">{{ __('customer/header.nav_shop_top_filter') }}</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="javascript:void(0)"
                                               data-bs-toggle="dropdown">{{ __('customer/header.nav_product') }}</a>

                                            <div class="dropdown-menu dropdown-menu-3 dropdown-menu-2">
                                                <div class="row">
                                                    <div class="col-xl-3">
                                                        <div class="dropdown-column m-0">
                                                            <h5 class="dropdown-header">
                                                                {{ __('customer/header.nav_product_pages') }} </h5>
                                                            <a class="dropdown-item"
                                                               href="product-left-thumbnail.html">{{ __('customer/header.nav_product_thumbnail') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-4-image.html">{{ __('customer/header.nav_product_images') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-slider.html">{{ __('customer/header.nav_product_slider') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-sticky.html">{{ __('customer/header.nav_product_sticky') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-accordion.html">{{ __('customer/header.nav_product_accordion') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-circle.html">{{ __('customer/header.nav_product_tab') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-digital.html">{{ __('customer/header.nav_product_digital') }}</a>

                                                            <h5 class="custom-mt dropdown-header">{{ __('customer/header.nav_product_features') }}
                                                            </h5>
                                                            <a class="dropdown-item"
                                                               href="product-circle.html">{{ __('customer/header.nav_bundle_cross_sale') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-left-thumbnail.html">{{ __('customer/header.nav_hot_stock_progress') }}
                                                                <label class="menu-label">{{ __('customer/header.nav_new') }}</label>
                                                            </a>
                                                            <a class="dropdown-item"
                                                               href="product-sold-out.html">{{ __('customer/header.nav_sold_out') }}</a>
                                                            <a class="dropdown-item" href="product-circle.html">
                                                                {{ __('customer/header.nav_sale_countdown') }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3">
                                                        <div class="dropdown-column m-0">
                                                            <h5 class="dropdown-header">
                                                                Product Variants Style </h5>
                                                            <a class="dropdown-item"
                                                               href="product-rectangle.html">Variant Rectangle</a>
                                                            <a class="dropdown-item"
                                                               href="product-circle.html">{{ __('customer/header.nav_variant_circle') }}
                                                                <label
                                                                        class="menu-label">{{ __('customer/header.nav_new') }}</label></a>
                                                            <a class="dropdown-item"
                                                               href="product-color-image.html">{{ __('customer/header.nav_variant_image_swatch') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-color.html">{{ __('customer/header.nav_variant_color') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-radio.html">{{ __('customer/header.nav_variant_radio_button') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-dropdown.html">{{ __('customer/header.nav_variant_dropdown') }}</a>
                                                            <h5 class="custom-mt dropdown-header">{{ __('customer/header.nav_product_features') }}
                                                            </h5>
                                                            <a class="dropdown-item"
                                                               href="product-left-thumbnail.html">{{ __('customer/header.nav_sticky_checkout') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-dynamic.html">{{ __('customer/header.nav_dynamic_checkout') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-sticky.html">{{ __('customer/header.nav_secure_checkout') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-bundle.html">{{ __('customer/header.nav_active_product_view') }}</a>
                                                            <a class="dropdown-item" href="product-bundle.html">
                                                                {{ __('customer/header.nav_active_last_orders') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3">
                                                        <div class="dropdown-column m-0">
                                                            <h5 class="dropdown-header">
                                                                {{ __('customer/header.nav_product_features') }} </h5>
                                                            <a class="dropdown-item"
                                                               href="product-image.html">{{ __('customer/header.nav_product_simple') }}</a>
                                                            <a class="dropdown-item" href="product-rectangle.html">
                                                                {{ __('customer/header.nav_product_classified') }}
                                                                <label
                                                                        class="menu-label">{{ __('customer/header.nav_new') }}</label>
                                                            </a>
                                                            <a class="dropdown-item"
                                                               href="product-size-chart.html">{{ __('customer/header.nav_size_chart') }}
                                                                <label
                                                                        class="menu-label">{{ __('customer/header.nav_new') }}</label></a>
                                                            <a class="dropdown-item"
                                                               href="product-size-chart.html">{{ __('customer/header.nav_delivery_return') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-size-chart.html">{{ __('customer/header.nav_product_review') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-expert.html">{{ __('customer/header.nav_ask_an_expert') }}</a>
                                                            <h5 class="custom-mt dropdown-header">{{ __('customer/header.nav_product_features') }}
                                                            </h5>
                                                            <a class="dropdown-item"
                                                               href="product-bottom-thumbnail.html">{{ __('customer/header.nav_product_tags') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-image.html">{{ __('customer/header.nav_store_information') }}</a>
                                                            <a class="dropdown-item"
                                                               href="product-image.html">{{ __('customer/header.nav_social_share') }}
                                                                <label
                                                                        class="menu-label warning-label">{{ __('customer/header.nav_hot') }}</label>
                                                            </a>
                                                            <a class="dropdown-item"
                                                               href="product-left-thumbnail.html">{{ __('customer/header.nav_related_products') }}
                                                                <label class="menu-label warning-label">{{ __('customer/header.nav_hot') }}</label>
                                                            </a>
                                                            <a class="dropdown-item"
                                                               href="product-right-thumbnail.html">{{ __('customer/header.nav_wishlist_compare') }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-3 d-xl-block d-none">
                                                        <div class="dropdown-column m-0">
                                                            <div class="menu-img-banner">
                                                                <a class="text-title" href="product-circle.html">
                                                                    <img src="{{asset('assets/images/mega-menu.png')}}"
                                                                         alt="banner">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="nav-item dropdown dropdown-mega">
                                            <a class="nav-link dropdown-toggle ps-xl-2 ps-0"
                                               href="javascript:void(0)"
                                               data-bs-toggle="dropdown">{{ __('customer/header.nav_mega_menu') }}</a>

                                            <div class="dropdown-menu dropdown-menu-2">
                                                <div class="row">
                                                    <div class="dropdown-column col-xl-3">
                                                        <h5 class="dropdown-header">{{ __('customer/header.nav_daily_vegetables') }}</h5>
                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_beans_brinjals') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_broccoli_cauliflower') }}</a>

                                                        <a href="shop-left-sidebar.html"
                                                           class="dropdown-item">{{ __('customer/header.nav_chilies_garlic') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_vegetables_salads') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_gourd_cucumber') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_herbs_sprouts') }}</a>

                                                        <a href="demo-personal-portfolio.html"
                                                           class="dropdown-item">{{ __('customer/header.nav_lettuce_leafy') }}</a>
                                                    </div>

                                                    <div class="dropdown-column col-xl-3">
                                                        <h5 class="dropdown-header">{{ __('customer/header.nav_baby_tender') }}</h5>
                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_beans_brinjals') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_broccoli_cauliflower') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_chilies_garlic') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_vegetables_salads') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_gourd_cucumber') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_potatoes_tomatoes') }}</a>

                                                        <a href="shop-left-sidebar.html"
                                                           class="dropdown-item">{{ __('customer/header.nav_peas_corn') }}</a>
                                                    </div>

                                                    <div class="dropdown-column col-xl-3">
                                                        <h5 class="dropdown-header">{{ __('customer/header.nav_exotic_vegetables') }}</h5>
                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_asparagus_artichokes') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_avocados_peppers') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_broccoli_zucchini') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_celery_fennel_leeks') }}</a>

                                                        <a class="dropdown-item"
                                                           href="shop-left-sidebar.html">{{ __('customer/header.nav_chilies_lime') }}</a>
                                                    </div>

                                                    <div class="dropdown-column dropdown-column-img col-3"></div>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="javascript:void(0)"
                                               data-bs-toggle="dropdown">{{ __('customer/header.nav_blog') }}</a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="blog-detail.html">{{ __('customer/header.nav_blog_detail') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="blog-grid.html">{{ __('customer/header.nav_blog_grid') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="blog-list.html">{{ __('customer/header.nav_blog_list') }}</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown new-nav-item">
                                            <a class="nav-link dropdown-toggle" href="javascript:void(0)"
                                               data-bs-toggle="dropdown">{{ __('customer/header.nav_pages') }} <label
                                                        class="new-dropdown">{{ __('customer/header.nav_new') }}</label></a>
                                            <ul class="dropdown-menu">
                                                <li class="sub-dropdown-hover">
                                                    <a class="dropdown-item"
                                                       href="javascript:void(0)">{{ __('customer/header.nav_email_template') }}
                                                        <span class="new-text"><i
                                                                    class="fa-solid fa-bolt-lightning"></i></span></a>
                                                    <ul class="sub-menu">
                                                        <li>
                                                            <a
                                                                    href="../email-templete/abandonment-email.html">{{ __('customer/header.nav_abandonment') }}</a>
                                                        </li>
                                                        <li>
                                                            <a href="../email-templete/offer-template.html">{{ __('customer/header.nav_offer_template') }}</a>
                                                        </li>
                                                        <li>
                                                            <a href="../email-templete/order-success.html">{{ __('customer/header.nav_order_success') }}</a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('password.request') }}">{{ __('customer/header.nav_reset_password') }}</a>
                                                        </li>
                                                        <li>
                                                            <a href="../email-templete/welcome.html">{{ __('customer/header.nav_welcome_template') }}</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li class="sub-dropdown-hover">
                                                    <a class="dropdown-item"
                                                       href="javascript:void(0)">{{ __('customer/header.nav_invoice_template') }}
                                                        <span class="new-text"><i
                                                                    class="fa-solid fa-bolt-lightning"></i></span></a>
                                                    <ul class="sub-menu">
                                                        <li>
                                                            <a href="../invoice/invoice-1.html">{{ __('customer/header.nav_invoice_1') }}</a>
                                                        </li>

                                                        <li>
                                                            <a href="../invoice/invoice-2.html">{{ __('customer/header.nav_invoice_2') }}</a>
                                                        </li>

                                                        <li>
                                                            <a href="../invoice/invoice-3.html">{{ __('customer/header.nav_invoice_3') }}</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="404.html">{{ __('customer/header.nav_404') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="about-us.html">{{ __('customer/header.nav_about_us') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="cart.html">{{ __('customer/header.nav_cart_menu') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="contact-us.html">{{ __('customer/header.nav_contact') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="checkout.html">{{ __('customer/header.nav_checkout_menu') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="coming-soon.html">{{ __('customer/header.nav_coming_soon') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="compare.html">{{ __('customer/header.nav_compare') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="faq.html">{{ __('customer/header.nav_faq') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="order-success.html">{{ __('customer/header.nav_order_success') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="order-tracking.html">{{ __('customer/header.nav_order_tracking') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="otp.html">{{ __('customer/header.nav_otp') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="search.html">{{ __('customer/header.nav_search') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="user-dashboard.html">{{ __('customer/header.nav_user_dashboard') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="wishlist.html">{{ __('customer/header.nav_wishlist_menu') }}</a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="javascript:void(0)"
                                               data-bs-toggle="dropdown">{{ __('customer/header.nav_seller') }}</a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="seller-become.html">{{ __('customer/header.nav_become_a_seller') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="seller-dashboard.html">{{ __('customer/header.nav_seller_dashboard') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="seller-detail.html">{{ __('customer/header.nav_seller_detail') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="seller-detail-2.html">{{ __('customer/header.nav_seller_detail_2') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="seller-grid.html">{{ __('customer/header.nav_seller_grid') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="seller-grid-2.html">{{ __('customer/header.nav_seller_grid_2') }}</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="header-nav-right">
                        <button class="btn deal-button" data-bs-toggle="modal" data-bs-target="#deal-box">
                            <i data-feather="zap"></i>
                            <span>{{ __('customer/header.nav_deal_today') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header End -->

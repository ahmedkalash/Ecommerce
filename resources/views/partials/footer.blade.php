<!-- Footer Modal Start -->
<footer class="section-t-space">
    <div class="container-fluid-lg">
        <div class="service-section">
            <div class="row g-3">
                <div class="col-12">
                    <div class="service-contain">
                        <div class="service-box">
                            <div class="service-image">
                                <img src="{{asset('assets/svg/product.svg')}}" class="blur-up lazyload" alt="">
                            </div>

                            <div class="service-detail">
                                <h5>{{ __('customer/footer.every_fresh_products') }}</h5>
                            </div>
                        </div>

                        <div class="service-box">
                            <div class="service-image">
                                <img src="{{asset('assets/svg/delivery.svg')}}" class="blur-up lazyload" alt="">
                            </div>

                            <div class="service-detail">
                                <h5>{{ __('customer/footer.free_delivery_for_order_over_50') }}</h5>
                            </div>
                        </div>

                        <div class="service-box">
                            <div class="service-image">
                                <img src="{{asset('assets/svg/discount.svg')}}" class="blur-up lazyload" alt="">
                            </div>

                            <div class="service-detail">
                                <h5>{{ __('customer/footer.daily_mega_discounts') }}</h5>
                            </div>
                        </div>

                        <div class="service-box">
                            <div class="service-image">
                                <img src="{{asset('assets/svg/market.svg')}}" class="blur-up lazyload" alt="">
                            </div>

                            <div class="service-detail">
                                <h5>{{ __('customer/footer.best_price_on_the_market') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-footer section-b-space section-t-space">
            <div class="row g-md-4 g-3">
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="footer-logo">
                        <div class="theme-logo">
                            <a href="{{route('home')}}">
                                <img src="{{asset("storage\\$footer_logo")}}" class="blur-up lazyload" alt="">
                            </a>
                        </div>

                        <div class="footer-logo-contain">
                            <p>{{ __('customer/footer.footer_text') }}</p>

                            <ul class="address">
                                <li>
                                    <i data-feather="home"></i>
                                    <a href="javascript:void(0)">1418 Riverwood Drive, CA 96052, US</a>
                                </li>
                                <li>
                                    <i data-feather="mail"></i>
                                    <a href="javascript:void(0)">support@fastkart.com</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <div class="footer-title">
                        <h4>{{ __('customer/footer.categories') }}</h4>
                    </div>

                    <div class="footer-contain">
                        <ul>
                            <li>
                                <a href="shop-left-sidebar.html"
                                   class="text-content">{{ __('customer/footer.vegetables_fruit') }}</a>
                            </li>
                            <li>
                                <a href="shop-left-sidebar.html"
                                   class="text-content">{{ __('customer/footer.beverages') }}</a>
                            </li>
                            <li>
                                <a href="shop-left-sidebar.html"
                                   class="text-content">{{ __('customer/footer.meats_seafood') }}</a>
                            </li>
                            <li>
                                <a href="shop-left-sidebar.html"
                                   class="text-content">{{ __('customer/footer.frozen_foods') }}</a>
                            </li>
                            <li>
                                <a href="shop-left-sidebar.html"
                                   class="text-content">{{ __('customer/footer.biscuits_snacks') }}</a>
                            </li>
                            <li>
                                <a href="shop-left-sidebar.html"
                                   class="text-content">{{ __('customer/footer.grocery_staples') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-xl col-lg-2 col-sm-3">
                    <div class="footer-title">
                        <h4>{{ __('customer/footer.useful_links') }}</h4>
                    </div>

                    <div class="footer-contain">
                        <ul>
                            <li>
                                <a href="{{route('home')}}" class="text-content">{{ __('customer/footer.home') }}</a>
                            </li>
                            <li>
                                <a href="shop-left-sidebar.html"
                                   class="text-content">{{ __('customer/footer.shop') }}</a>
                            </li>
                            <li>
                                <a href="about-us.html" class="text-content">{{ __('customer/footer.about_us') }}</a>
                            </li>
                            <li>
                                <a href="blog-list.html" class="text-content">{{ __('customer/footer.blog') }}</a>
                            </li>
                            <li>
                                <a href="contact-us.html"
                                   class="text-content">{{ __('customer/footer.contact_us') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-xl-2 col-sm-3">
                    <div class="footer-title">
                        <h4>{{ __('customer/footer.help_center') }}</h4>
                    </div>

                    <div class="footer-contain">
                        <ul>
                            <li>
                                <a href="order-success.html"
                                   class="text-content">{{ __('customer/footer.your_order') }}</a>
                            </li>
                            <li>
                                <a href="user-dashboard.html"
                                   class="text-content">{{ __('customer/footer.your_account') }}</a>
                            </li>
                            <li>
                                <a href="order-tracking.html"
                                   class="text-content">{{ __('customer/footer.track_order') }}</a>
                            </li>
                            <li>
                                <a href="wishlist.html"
                                   class="text-content">{{ __('customer/footer.your_wishlist') }}</a>
                            </li>
                            <li>
                                <a href="search.html" class="text-content">{{ __('customer/footer.search') }}</a>
                            </li>
                            <li>
                                <a href="faq.html" class="text-content">{{ __('customer/footer.faq') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="footer-title">
                        <h4>{{ __('customer/footer.contact_us_title') }}</h4>
                    </div>

                    <div class="footer-contact">
                        <ul>
                            <li>
                                <div class="footer-number">
                                    <i data-feather="phone"></i>
                                    <div class="contact-number">
                                        <h6 class="text-content">{{ __('customer/footer.hotline_24_7') }}</h6>
                                        <h5>+91 888 104 2340</h5>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="footer-number">
                                    <i data-feather="mail"></i>
                                    <div class="contact-number">
                                        <h6 class="text-content">{{ __('customer/footer.email_address') }}</h6>
                                        <h5>fastkart@hotmail.com</h5>
                                    </div>
                                </div>
                            </li>

                            <li class="social-app mb-0">
                                <h5 class="mb-2 text-content">{{ __('customer/footer.download_app') }}</h5>
                                <ul>
                                    <li class="mb-0">
                                        <a href="https://play.google.com/store/apps" target="_blank">
                                            <img src="{{asset('assets/images/playstore.svg')}}" class="blur-up lazyload"
                                                 alt="">
                                        </a>
                                    </li>
                                    <li class="mb-0">
                                        <a href="https://www.apple.com/in/app-store/" target="_blank">
                                            <img src="{{asset('assets/images/appstore.svg')}}" class="blur-up lazyload"
                                                 alt="">
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="sub-footer section-small-space">
            <div class="reserve">
                <h6 class="text-content">{{ __('customer/footer.all_rights_reserved') }}</h6>
            </div>

            <div class="payment">
                <img src="{{asset('assets/images/payment/1.png')}}" class="blur-up lazyload" alt="">
            </div>

            <div class="social-link">
                <h6 class="text-content">{{ __('customer/footer.stay_connected') }}</h6>
                <ul>
                    <li>
                        <a href="https://www.facebook.com/" target="_blank">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://twitter.com/" target="_blank">
                            <i class="fa-brands fa-twitter"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://in.pinterest.com/" target="_blank">
                            <i class="fa-brands fa-pinterest-p"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->

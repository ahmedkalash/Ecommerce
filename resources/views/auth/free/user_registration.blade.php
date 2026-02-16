@extends('auth.layouts.authentication')

@section('content')
    <!-- aiz-main-wrapper -->
    <div class="aiz-main-wrapper d-flex flex-column justify-content-center bg-white">
        <section class="bg-white overflow-hidden" style="min-height:100vh;">
            <div class="row" style="min-height: 100vh;">
                <!-- Left Side Image-->
                <div class="col-xxl-6 col-lg-7">
                    <div class="h-100">
                        <img src="{{ get_file_by_id(get_setting('customer_register_page_image')) }}" alt=""
                             class="img-fit h-100">
                    </div>
                </div>

                <!-- Right Side -->
                <div class="col-xxl-6 col-lg-5">
                    <div class="right-content">
                        <div class="row align-items-center justify-content-center justify-content-lg-start h-100">
                            <div class="col-xxl-6 p-4 p-lg-5">
                                <!-- Site Icon -->
                                <div class="size-48px mb-3 mx-auto mx-lg-0">
                                    <img src="{{ get_file_by_id(get_setting('site_icon')) }}"
                                         alt="{{ translate('Site Icon')}}" class="img-fit h-100">
                                </div>
                                <!-- Titles -->
                                <div class="text-center text-lg-left">
                                    <h1 class="fs-20 fs-md-24 fw-700 text-primary"
                                        style="text-transform: uppercase;">{{ translate('Create an account')}}</h1>
                                </div>
                                <!-- Register form -->
                                <div class="pt-3 pt-lg-4 bg-white">
                                    <div class="">
                                        <form id="reg-form" class="form-default" role="form"
                                              action="{{ route('register') }}" method="POST">
                                            @csrf
                                            <!-- Name -->
                                            <div class="form-group">
                                                <label for="name"
                                                       class="fs-12 fw-700 text-soft-dark">{{ translate('Full Name') }}</label>
                                                <input type="text"
                                                       class="form-control rounded-0{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                       value="{{ old('name') }}"
                                                       placeholder="{{  translate('Full Name') }}" name="name">
                                                @if ($errors->has('name'))
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                                    @enderror
                                                    @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                                    @enderror
                                            </div>

                                            <!-- Email or Phone -->
                                            {{-- @if (addon_is_activated('otp_system'))
                                                    <div class="form-group phone-form-group mb-1">
                                                        <label for="phone" class="fs-12 fw-700 text-soft-dark">{{  translate('Phone') }}</label>
                                            <input type="tel" id="phone-code" class="form-control rounded-0 @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                                            @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                    </div>

                                    <input type="hidden" name="country_code" value="">

                                    <div class="form-group email-form-group mb-1 d-none">
                                        <label for="email" class="fs-12 fw-700 text-soft-dark">{{ translate('Email') }}</label>
                                        <input type="email" class="form-control rounded-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email" autocomplete="off">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group text-right">
                                        <button class="btn btn-link p-0 text-primary" type="button" onclick="toggleEmailPhone(this)"><i>*{{ translate('Use Email Instead') }}</i></button>
                                    </div>
                                    @else
                                    <div class="form-group">
                                        <label for="email" class="fs-12 fw-700 text-soft-dark">{{ translate('Email') }}</label>
                                        <input type="email" class="form-control rounded-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    @endif --}}


                                            @if (addon_is_activated('otp_system'))
                                                @if($phone)
                                                    {{-- Show only the phone field if $phone exists --}}
                                                    <div class="form-group phone-form-group mb-1">
                                                        <label for="phone"
                                                               class="fs-12 fw-700 text-soft-dark">{{ translate('Phone') }}</label>
                                                        <input type="tel" id="phone-code"
                                                               class="form-control rounded-0 @error('phone') is-invalid @enderror"
                                                               value="{{ $phone }}" placeholder="" name="phone"
                                                               autocomplete="off" readonly>
                                                        {{-- <input type="hidden" name="country_code" value="{{ $country_code ?? '' }}"> --}}
                                                        <input type="hidden" name="country_code" value="">
                                                        @error('phone')
                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                        @enderror
                                                    </div>
                                                @elseif($email)
                                                    {{-- Show only the email field if $email exists --}}
                                                    <div class="form-group email-form-group mb-1">
                                                        <label for="email"
                                                               class="fs-12 fw-700 text-soft-dark">{{ translate('Email') }}</label>
                                                        <input type="email"
                                                               class="form-control rounded-0 @error('email') is-invalid @enderror"
                                                               value="{{ $email }}"
                                                               placeholder="{{ translate('Email') }}" name="email"
                                                               autocomplete="off" readonly>
                                                        @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                        @enderror
                                                    </div>
                                                @else
                                                    {{-- Show both fields with the toggle button if neither email nor phone is set --}}
                                                    <div class="form-group phone-form-group mb-1">
                                                        <label for="phone"
                                                               class="fs-12 fw-700 text-soft-dark">{{ translate('Phone') }}</label>
                                                        <input type="tel" id="phone-code"
                                                               class="form-control rounded-0 @error('phone') is-invalid @enderror"
                                                               value="{{ old('phone') }}" placeholder="" name="phone"
                                                               autocomplete="off">
                                                        @error('phone')
                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                        @enderror
                                                    </div>

                                                    <input type="hidden" id="country_code" name="country_code"
                                                           value="{{ old('country_code', 'US') }}"> {{-- Default to 'US' --}}

                                                    <div class="form-group email-form-group mb-1 d-none">
                                                        <label for="email"
                                                               class="fs-12 fw-700 text-soft-dark">{{ translate('Email') }}</label>
                                                        <input type="email"
                                                               class="form-control rounded-0 @error('email') is-invalid @enderror"
                                                               value="{{ old('email') }}"
                                                               placeholder="{{ translate('Email') }}" name="email"
                                                               autocomplete="off">
                                                        @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group text-right">
                                                        <button class="btn btn-link p-0 text-primary" type="button"
                                                                onclick="toggleEmailPhone(this)">
                                                            <i>*{{ translate('Use Email Instead') }}</i>
                                                        </button>
                                                    </div>
                                                @endif
                                            @else
                                                {{-- If OTP system is disabled, show only the email field --}}
                                                <div class="form-group">
                                                    <label for="email"
                                                           class="fs-12 fw-700 text-soft-dark">{{ translate('Email') }}</label>
                                                    <input type="email"
                                                           class="form-control rounded-0 @error('email') is-invalid @enderror"
                                                           value="{{ $email ?? old('email') }}"
                                                           placeholder="{{ translate('Email') }}"
                                                           name="email" {{$email  ? 'readonly' : ''}}>
                                                    @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                    @enderror
                                                </div>
                                            @endif

                                            <!-- password -->
                                            <div class="form-group mb-0">
                                                <label for="password"
                                                       class="fs-12 fw-700 text-soft-dark">{{ translate('Password') }}</label>
                                                <div class="position-relative">
                                                    <input type="password"
                                                           class="form-control rounded-0 @error('password') is-invalid @enderror"
                                                           placeholder="{{  translate('Password') }}" name="password">
                                                    <i class="password-toggle las la-2x la-eye"></i>
                                                </div>
                                                <div class="text-right mt-1">
                                                    <span
                                                        class="fs-12 fw-400 text-gray-dark">{{ translate('Password must contain at least 8 digits') }}</span>
                                                </div>
                                                @error('password')
                                                <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>

                                            <!-- password Confirm -->
                                            <div class="form-group">
                                                <label for="password_confirmation"
                                                       class="fs-12 fw-700 text-soft-dark">{{ translate('Confirm Password') }}</label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control rounded-0"
                                                           placeholder="{{  translate('Confirm Password') }}"
                                                           name="password_confirmation">
                                                    <i class="password-toggle las la-2x la-eye"></i>
                                                </div>
                                            </div>

                                            <!-- Recaptcha -->
                                            @if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_register') == 1)

                                                @error('g-recaptcha-response')
                                                <span
                                                    class="border invalid-feedback rounded p-2 mb-3 bg-danger text-white"
                                                    role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                                                @enderror
                                            @endif

                                            <!-- Terms and Conditions -->
                                            <div class="mb-3">
                                                <label class="aiz-checkbox">
                                                    <input type="checkbox" name="agree_to_terms" required>
                                                    <span class="">{{ translate('By signing up you agree to our ')}} <a
                                                            href="{{ route('terms') }}"
                                                            class="fw-500">{{ translate('terms and conditions.') }}</a></span>
                                                    <span class="aiz-square-check"></span>
                                                </label>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="mb-4 mt-4">
                                                <button type="submit"
                                                        class="btn btn-primary btn-block fw-600 rounded-0">{{ translate('Create Account') }}</button>
                                            </div>
                                        </form>

                                        <!-- Social Login -->
                                        @if(get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1 || get_setting('apple_login') == 1)
                                            <div class="text-center mb-3">
                                                <span
                                                    class="bg-white fs-12 text-gray">{{ translate('Or Join With')}}</span>
                                            </div>
                                            <ul class="list-inline social colored text-center mb-4">
                                                @if (get_setting('facebook_login') == 1)
                                                    <li class="list-inline-item">
                                                        <a href="{{ route('social.login', ['provider' => 'facebook']) }}"
                                                           class="facebook">
                                                            <i class="lab la-facebook-f"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(get_setting('google_login') == 1)
                                                    <li class="list-inline-item">
                                                        <a href="{{ route('social.login', ['provider' => 'google']) }}"
                                                           class="google">
                                                            <i class="lab la-google"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if (get_setting('twitter_login') == 1)
                                                    <li class="list-inline-item">
                                                        <a href="{{ route('social.login', ['provider' => 'twitter']) }}"
                                                           class="twitter">
                                                            <i class="lab la-twitter"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if (get_setting('apple_login') == 1)
                                                    <li class="list-inline-item">
                                                        <a href="{{ route('social.login', ['provider' => 'apple']) }}"
                                                           class="apple">
                                                            <i class="lab la-apple"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        @endif
                                    </div>

                                    <!-- Log In -->
                                    <p class="fs-12 text-gray mb-0">
                                        {{ translate('Already have an account?')}}
                                        <a href="{{ route('user.login') }}"
                                           class="ml-2 fs-14 fw-700 animate-underline-primary">{{ translate('Log In')}}</a>
                                    </p>
                                    <!-- Go Back -->
                                    <a href="{{ url()->previous() }}"
                                       class="mt-3 fs-14 fw-700 d-flex align-items-center text-primary"
                                       style="max-width: fit-content;">
                                        <i class="las la-arrow-left fs-20 mr-1"></i>
                                        {{ translate('Back to Previous Page')}}
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

@section('script')
    @if(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_register') == 1)
        <script src="https://www.google.com/recaptcha/api.js?render={{ env('CAPTCHA_KEY') }}"></script>

        <script type="text/javascript">
            document.getElementById('reg-form').addEventListener('submit', function (e) {
                e.preventDefault();
                grecaptcha.ready(function () {
                    grecaptcha.execute(`{{ env('CAPTCHA_KEY') }}`, {
                        action: 'register'
                    }).then(function (token) {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'hidden');
                        input.setAttribute('name', 'g-recaptcha-response');
                        input.setAttribute('value', token);
                        e.target.appendChild(input);

                        e.target.submit();
                    });
                });
            });
        </script>
    @else
        <script type="text/javascript">
            // Add loading state to prevent double submission
            document.getElementById('reg-form').addEventListener('submit', function (e) {
                var submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return false;
                }
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>{{ translate("Creating Account...") }}';
            });
        </script>
    @endif

@endsection

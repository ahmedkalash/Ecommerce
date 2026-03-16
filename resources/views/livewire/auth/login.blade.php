<form wire:submit="authenticate" class="row g-4">
    <div class="col-12">
        <div class="form-floating theme-form-floating log-in-form">
            <input type="email" class="form-control" id="email" wire:model="email"
                   placeholder="{{ __('customer/login_page.email_address') }}">
            <label for="email">{{ __('customer/login_page.email_address') }}</label>
            @error('email') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating theme-form-floating log-in-form">
            <input type="password" class="form-control" id="password" wire:model="password"
                   placeholder="{{ __('customer/login_page.password') }}">
            <label for="password">{{ __('customer/login_page.password') }}</label>
            @error('password') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="forgot-box">
            <div class="form-check ps-0 m-0 remember-box">
                <input class="checkbox_animated check-box" type="checkbox" wire:model="remember"
                       id="flexCheckDefault">
                <label class="form-check-label"
                       for="flexCheckDefault">{{ __('customer/login_page.remember_me') }}</label>
            </div>
            <a href="{{ route('password.request') }}"
               class="forgot-password">{{ __('customer/login_page.forgot_password') }}</a>
        </div>
    </div>

    <div class="col-12">
        <button class="btn btn-animation w-100 justify-content-center" type="submit">
            <span wire:loading.remove wire:target="authenticate">{{ __('customer/login_page.log_in') }}</span>
            <span wire:loading wire:target="authenticate">{{ __('customer/login_page.please_wait') }}</span>
        </button>
    </div>
</form>
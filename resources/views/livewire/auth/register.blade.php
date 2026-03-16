<form class="row g-4" wire:submit.prevent="register">
    <div class="col-12">
        <div class="form-floating theme-form-floating">
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="fullname"
                   wire:model.live="name"
                   placeholder="{{ __('customer/register_page.full_name') }}">
            <label for="fullname">{{ __('customer/register_page.full_name') }}</label>
            @error('name') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-floating theme-form-floating">
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                   wire:model.live="email"
                   placeholder="{{ __('customer/register_page.email_address') }}">
            <label for="email">{{ __('customer/register_page.email_address') }}</label>
            @error('email') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="form-floating theme-form-floating">
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                   wire:model.live="password"
                   placeholder="{{ __('customer/register_page.password') }}">
            <label for="password">{{ __('customer/register_page.password') }}</label>
            @error('password') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-floating theme-form-floating">
            <input type="password" class="form-control" id="password_confirmation"
                   wire:model.live="password_confirmation"
                   placeholder="{{ __('customer/register_page.password_confirmation') }}">
            <label for="password_confirmation">{{ __('customer/register_page.password_confirmation') }}</label>
        </div>
    </div>

    <div class="col-12">
        <div class="forgot-box">
            <div class="form-check ps-0 m-0 remember-box">
                <input class="checkbox_animated check-box @error('agree_to_terms') is-invalid @enderror" type="checkbox"
                       id="flexCheckDefault" wire:model="agree_to_terms">
                <label class="form-check-label" for="flexCheckDefault">{{ __('customer/register_page.i_agree_with') }}
                    <span>{{ __('customer/register_page.terms') }}</span> {{ __('customer/register_page.and') }}
                    <span>{{ __('customer/register_page.privacy') }}</span></label>
                @error('agree_to_terms') <span class="text-danger mt-1 d-block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="col-12">
        <button class="btn btn-animation w-100" type="submit">{{ __('customer/register_page.sign_up') }}</button>
    </div>
</form>
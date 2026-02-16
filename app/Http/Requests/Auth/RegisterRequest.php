<?php

namespace App\Http\Requests\Auth;

use App\Enums\RecaptchaAction;
use App\Rules\Recaptcha;
use App\Services\RecaptchaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required_without:phone', 'nullable', 'email', 'unique:users,email', 'string', 'max:190'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required_without:email', 'nullable', 'string', 'min:6', 'max:190', 'unique:users,phone'], // Todo: otp plugin
            'agree_to_terms' => ['required', 'string', 'accepted', 'max:10'],
            'g-recaptcha-response' => RecaptchaService::validationRules(RecaptchaAction::CUSTOMER_REGISTER),
        ];
    }

    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            // 'phone' => '+'.$this->country_code.$this->phone,
        ]);
    }
}

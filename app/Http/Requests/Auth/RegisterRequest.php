<?php

namespace App\Http\Requests\Auth;

use App\Rules\Recaptcha;
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
            'email' => ['required', 'email', 'unique:users,id', 'string', 'max:190'],
            'password' => ['required', 'confirmed', Password::min(8)->max(190)],
            // 'phone' => ['nullable', 'string', 'min:6', 'max:190'], Todo: otp plugin
            'agree_to_terms' => ['required', 'string', 'accepted', 'max:10'],
            'g-recaptcha-response' => [
                Rule::when(
                    get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_register') == 1,
                    ['required', new Recaptcha],
                    ['sometimes']
                ),
            ],
        ];
    }

    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            // 'phone' => '+'.$this->country_code.$this->phone,
        ]);
    }
}

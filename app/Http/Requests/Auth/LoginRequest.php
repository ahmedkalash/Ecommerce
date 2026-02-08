<?php

namespace App\Http\Requests\Auth;

use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'email', 'string', 'max:190'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
            'g-recaptcha-response' => [
                Rule::when(
                    get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_register') == 1,
                    ['required', new Recaptcha],
                    ['sometimes']
                ),
            ],
        ];
    }
}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        $data = [
            'secret' => config('services.recaptcha.secret'),
            'response' => $value,
        ];

        try {
            $response = Http::asForm()->post(config('services.recaptcha.verify_url'), $data);

            $recaptchaData = $response->json();

            return ($recaptchaData['success'] ?? false) &&
                ($recaptchaData['score'] ?? 0) >= (float) config('services.recaptcha.score_threshold', 0.5);
        } catch (\Exception $e) {
            Log::error('Recaptcha validation failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return translate('Verification failed. Please try again.');
    }
}

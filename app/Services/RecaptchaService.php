<?php

namespace App\Services;

use App\Enums\RecaptchaAction;
use App\Rules\Recaptcha;

class RecaptchaService
{
    /**
     * Get the validation rules for Google Recaptcha based on the specific action or context.
     *
     * @param  RecaptchaAction|null  $action  The context action enum
     */
    public static function validationRules(?RecaptchaAction $action = null): array
    {
        // 1. Check if Google Recaptcha is globally enabled
        if (get_setting('google_recaptcha') != 1) {
            return [];
        }

        // 2. If the action is specific, check its specific setting
        if ($settingKey = $action?->value) {
            // E.g. check if 'recaptcha_forgot_password' is ON
            if (get_setting($settingKey) != 1) {
                return [];
            }
        }

        // 4. Return the validation rule
        return ['required', new Recaptcha];
    }
}

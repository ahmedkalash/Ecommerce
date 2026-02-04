<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationVerificationCode;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Rules\Recaptcha;
use App\Services\SendSmsService;
use App\Utility\EmailUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VerificationFirstController extends Controller
{
    public function verifyRegEmailorPhone()
    {
        $type = 'customer';
        if (Auth::check()) {
            if ((Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'seller')) {
                flash(translate('Admin or seller cannot be a customer'))->error();

                return back();
            }
            if (Auth::user()->user_type == 'customer') {
                flash(translate('This user already a customer'))->error();

                return back();
            }
        } else {
            return view(
                'auth.'.get_setting('authentication_layout_select').'.customer_reg_verification',
                compact('type')
            );
        }
    }

    public function sendRegVerificationCode(Request $request)
    {
        $request->validate([
            'g-recaptcha-response' => [
                Rule::when(
                    get_setting('google_recaptcha') == 1 && get_setting('recaptcha_customer_mail_verification') == 1,
                    ['required', new Recaptcha],
                    ['sometimes']
                ),
            ],
        ]);

        $email = $request->email ?? null;
        $phone = $request->phone != null ? '+'.$request->country_code.$request->phone : null;

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $email)->first();
            if ($user != null) {
                flash(translate('Email already exists.'))->error();

                return back();
            }
        } elseif (User::where('phone', $phone)->first() != null) {
            flash(translate('Phone already exists.'))->error();

            return back();
        }

        $verificationCode = rand(100000, 999999);
        $customerVerification = RegistrationVerificationCode::updateOrCreate(
            ['email' => $email, 'phone' => $phone],
            ['code' => $verificationCode]
        );
        $success = 1;

        if ($email) {
            try {
                EmailUtility::email_verification_for_registration_customer(
                    'email_verification_for_registration_customer',
                    $email,
                    $verificationCode
                );
            } catch (\Exception $e) {
                $success = 0;
            }
        } else {
            if (addon_is_activated('otp_system')) {
                $sms_template = SmsTemplate::where('identifier', 'phone_number_verification')->first();
                $sms_body = $sms_template->sms_body;
                $sms_body = str_replace('[[code]]', $verificationCode, $sms_body);
                $sms_body = str_replace('[[site_name]]', env('APP_NAME'), $sms_body);
                $template_id = $sms_template->template_id;

                (new SendSmsService)->sendSMS($phone, env('APP_NAME'), $sms_body, $template_id);
            }
        }

        if ($success) {
            return redirect()->route('customer-reg.verify_code', encrypt($customerVerification->id));
        } else {
            flash(translate('Something went wrong!'))->error();

            return back();
        }
    }

    public function regVerifyCode($id)
    {
        $customerVerification = RegistrationVerificationCode::whereId(decrypt($id))->first();

        return view(
            'auth.'.get_setting('authentication_layout_select').'.customer_verify_confirmation',
            compact('customerVerification')
        );
    }

    public function regVerifyCodeConfirmation(Request $request)
    {
        $email = isset($request->email) ? $request->email : null;
        $phone = isset($request->phone) ? $request->phone : null;

        $customerVerification = RegistrationVerificationCode::where('code', $request->verification_code);
        $customerVerification = $request->email != null ?
            $customerVerification->where('email', $email) :
            $customerVerification->where('phone', $phone);
        $customerVerification = $customerVerification->first();
        if ($customerVerification == null) {
            flash(translate('Verification code do not matched'))->error();

            return back();
        } else {
            $customerVerification->is_verified = 1;
            $customerVerification->save();

            return view(
                'auth.'.get_setting('authentication_layout_select').'.user_registration',
                compact('customerVerification', 'email', 'phone')
            );
        }
    }
}

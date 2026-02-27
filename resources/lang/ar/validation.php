<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted' => 'يجب قبول :attribute.',
    'active_url' => ':attribute لا يمثّل رابطاً صحيحاً.',
    'after' => 'يجب أن يكون :attribute تاريخاً بعد :date.',
    'after_or_equal' => 'يجب أن يكون :attribute تاريخاً بعد أو يساوي :date.',
    'alpha' => 'يجب أن لا يحتوي :attribute إلا على حروف.',
    'alpha_dash' => 'يجب أن يحتوي :attribute على حروف وأرقام وشرطات فقط.',
    'alpha_num' => 'يجب أن لا يحتوي :attribute إلا على حروف وأرقام.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'before' => 'يجب أن يكون :attribute تاريخاً قبل :date.',
    'before_or_equal' => 'يجب أن يكون :attribute تاريخاً قبل أو يساوي :date.',
    'between' => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون عدد حروف :attribute بين :min و :max.',
        'array' => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max.',
    ],
    'boolean' => 'يجب أن يكون :attribute إما true أو false.',
    'confirmed' => 'حقل تأكيد :attribute غير متطابق.',
    'date' => ':attribute ليس تاريخاً صحيحاً.',
    'date_format' => ':attribute لا يتوافق مع الصيغة :format.',
    'different' => 'يجب أن يختلف :attribute عن :other.',
    'digits' => 'يجب أن يتكوّن :attribute من :digits رقماً.',
    'digits_between' => 'يجب أن يكون :attribute بين :min و :max أرقام.',
    'dimensions' => 'أبعاد الصورة في :attribute غير صالحة.',
    'distinct' => 'الحقل :attribute يحتوي قيمة مكررة.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح.',
    'exists' => 'القيمة المختارة في :attribute غير صالحة.',
    'file' => 'يجب أن يكون :attribute ملفاً.',
    'filled' => 'يجب أن يحتوي الحقل :attribute على قيمة.',
    'gt' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'file' => 'يجب أن يكون حجم الملف :attribute أكبر من :value كيلوبايت.',
        'string' => 'يجب أن يكون عدد حروف :attribute أكبر من :value.',
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عناصر.',
    ],
    'gte' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :value.',
        'file' => 'يجب أن يكون حجم الملف :attribute أكبر من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يكون عدد حروف :attribute أكبر من أو يساوي :value.',
        'array' => 'يجب أن يحتوي :attribute على :value عناصر أو أكثر.',
    ],
    'image' => 'يجب أن يكون :attribute صورة.',
    'in' => 'القيمة المختارة في :attribute غير صالحة.',
    'in_array' => 'الحقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون :attribute عدداً صحيحاً.',
    'ip' => 'يجب أن يكون :attribute عنوان IP صحيحاً.',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صحيحاً.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صحيحاً.',
    'json' => 'يجب أن يكون :attribute نصّ JSON صالحاً.',
    'lt' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من :value.',
        'file' => 'يجب أن يكون حجم الملف :attribute أصغر من :value كيلوبايت.',
        'string' => 'يجب أن يكون عدد حروف :attribute أصغر من :value.',
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عناصر.',
    ],
    'lte' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من أو تساوي :value.',
        'file' => 'يجب أن يكون حجم الملف :attribute أصغر من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يكون عدد حروف :attribute أصغر من أو يساوي :value.',
        'array' => 'يجب ألّا يحتوي :attribute على أكثر من :value عناصر.',
    ],
    'max' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من أو تساوي :max.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت.',
        'string' => 'يجب أن لا يتجاوز عدد حروف :attribute :max.',
        'array' => 'يجب ألّا يحتوي :attribute على أكثر من :max عناصر.',
    ],
    'mimes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'mimetypes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'min' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :min.',
        'file' => 'يجب أن لا يقل حجم الملف :attribute عن :min كيلوبايت.',
        'string' => 'يجب أن لا يقل عدد حروف :attribute عن :min.',
        'array' => 'يجب أن يحتوي :attribute على الأقل على :min عناصر.',
    ],
    'not_in' => 'القيمة المختارة في :attribute غير صالحة.',
    'not_regex' => 'صيغة :attribute غير صالحة.',
    'numeric' => 'يجب أن يكون :attribute رقماً.',
    'present' => 'يجب أن يكون الحقل :attribute موجوداً.',
    'regex' => 'صيغة :attribute غير صالحة.',
    'required' => 'الحقل :attribute مطلوب.',
    'required_if' => 'الحقل :attribute مطلوب حين يكون :other هو :value.',
    'required_unless' => 'الحقل :attribute مطلوب ما لم يكن :other في :values.',
    'required_with' => 'الحقل :attribute مطلوب حين يتوفر :values.',
    'required_with_all' => 'الحقل :attribute مطلوب حين يتوفر :values.',
    'required_without' => 'الحقل :attribute مطلوب حين لا يتوفر :values.',
    'required_without_all' => 'الحقل :attribute مطلوب حين لا يتوفر أيٌّ من :values.',
    'same' => 'يجب أن يتطابق :attribute مع :other.',
    'size' => [
        'numeric' => 'يجب أن تكون قيمة :attribute :size.',
        'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت.',
        'string' => 'يجب أن يكون عدد حروف :attribute :size.',
        'array' => 'يجب أن يحتوي :attribute على :size عناصر.',
    ],
    'string' => 'يجب أن يكون :attribute نصاً.',
    'timezone' => 'يجب أن يكون :attribute منطقة زمنية صحيحة.',
    'unique' => 'قيمة :attribute مستخدمة من قبل.',
    'uploaded' => 'فشل تحميل :attribute.',
    'url' => 'صيغة :attribute غير صالحة.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'phone' => 'رقم الهاتف',
        'address' => 'العنوان',
        'city' => 'المدينة',
        'country' => 'الدولة',
        'message' => 'الرسالة',
        'file' => 'الملف',
        'image' => 'الصورة',
        'date' => 'التاريخ',
        'title' => 'العنوان',
        'description' => 'الوصف',
    ],

];

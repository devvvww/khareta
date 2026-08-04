<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'حقل :attribute يجب أن يكون نصاً.',
    'max' => [
        'string' => 'حقل :attribute يجب ألا يتجاوز :max حرفاً.',
        'numeric' => 'حقل :attribute يجب ألا يتجاوز :max.',
    ],
    'min' => [
        'string' => 'حقل :attribute يجب ألا يقل عن :min أحرف.',
    ],
    'digits' => 'حقل :attribute يجب أن يتكون من :digits أرقام.',
    'unique' => 'قيمة حقل :attribute مستخدمة بالفعل.',
    'exists' => 'القيمة المحددة لحقل :attribute غير صحيحة.',
    'boolean' => 'حقل :attribute يجب أن يكون صحيحاً أو خاطئاً.',
    'array' => 'حقل :attribute يجب أن يكون قائمة.',

    'attributes' => [
        'name' => 'الاسم',
        'code' => 'الرمز',
        'code_number' => 'رقم المادة',
        'color' => 'اللون',
        'prefix' => 'رمز القسم',
        'primary_prefix' => 'الرمز الأساسي',
        'department_id' => 'القسم',
        'is_elective' => 'مادة اختيارية',
        'prerequisites' => 'المتطلبات السابقة',
        'prefixes.*' => 'احدى الرموز'
    ],
];
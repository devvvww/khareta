<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentManageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:10|unique:departments,prefix',
            'color' => [
                'nullable',
                'string',
                'max:7',
                function ($attribute, $value, $fail) {
                    $reserved = ['#0b7af1', '#10b981'];
                    if ($value && in_array(strtolower($value), $reserved)) {
                        $fail('هذا اللون محجوز ولا يمكن استخدام.');
                    }
                },
            ],
            'allows_electives' => 'nullable|boolean',
            'prefixes' => 'nullable|array',
            'prefixes.*' => 'required|string|max:10|distinct|unique:department_prefixes,prefix'
        ];
    }
}

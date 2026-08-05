<?php

namespace App\Http\Requests\Admin;

use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCourseManageRequest extends FormRequest
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
        $department = $this->route('department');
        $departmentIds = [$department->id];

        if (!$department->is_general) {
            $general = \App\Models\Department::where('is_general', true)->first();
            if ($general) {
                $departmentIds[] = $general->id;
            }
        }

        return [
            'code_number' => 'required|digits:3',
            'department_prefix_id' => [
                'required',
                Rule::exists('department_prefixes', 'id')->where('department_id', $department->id),
            ],
            'name' => 'required|string|max:255',
            'is_elective' => 'nullable|boolean',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => Rule::exists('courses', 'id')->whereIn('department_id', $departmentIds),
        ];
    }
    
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled('department_prefix_id') || !$this->filled('code_number')) {
                return;
            }

            $prefix = \App\Models\DepartmentPrefix::find($this->input('department_prefix_id'));
            if (!$prefix) {
                return;
            }

            $fullCode = $prefix->prefix . $this->input('code_number');

            if (Course::where('code', $fullCode)->exists()) {
                $validator->errors()->add('code_number', 'رمز المادة مستخدم بالفعل.');
            }
        });
    }
}

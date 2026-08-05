<?php

namespace App\Http\Requests\Admin;

use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCourseManageRequest extends FormRequest
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
        $departmentId = $this->route('course')->department_id;

        return [
            'code_number' => 'required|digits:3',
            'department_prefix_id' => [
                'required',
                Rule::exists('department_prefixes', 'id')->where('department_id', $departmentId),
            ],
            'name' => 'required|string|max:255',
            'is_elective' => 'nullable|boolean',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => Rule::exists('courses', 'id')->where('department_id', $departmentId),
            'prerequisites.*' => [
                Rule::exists('courses', 'id')->where('department_id', $departmentId),
                function ($attribute, $value, $fail) {
                    $course = $this->route('course'); // omit this line + check entirely in Store request (new course can't have this problem yet)
                    if ($course && $course->requiredForCourses()->where('courses.id', $value)->exists()) {
                        $fail('اختيار غير صالح — سيؤدي هذا إلى تبعية دائرية.');
                    }
                },
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled('department_prefix_id') || !$this->filled('code_number')) {
                return;
            }

            $course = $this->route('course');
            $prefix = \App\Models\DepartmentPrefix::find($this->input('department_prefix_id'));
            if (!$prefix) {
                return;
            }

            $fullCode = $prefix->prefix . $this->input('code_number');

            $exists = Course::where('code', $fullCode)
                ->where('id', '!=', $course->id)
                ->exists();

            if ($exists) {
                $validator->errors()->add('code_number', 'رمز المادة مستخدم بالفعل.');
            }
        });
    }
}

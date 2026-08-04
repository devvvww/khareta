<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentManageRequest extends FormRequest
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
        $primaryPrefix = $department->prefixes()->oldest()->first();

        return [
            'name' => 'required|string|max:255',
            'primary_prefix' => 'required|string|max:10|unique:department_prefixes,prefix,' . $primaryPrefix?->id,
            'color' => 'nullable|string|max:7',
            'allows_electives' => 'nullable|boolean',
        ];
    }
}

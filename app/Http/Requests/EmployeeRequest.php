<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'company_id' => 'required',
            'position_id' => 'required',
            'password' => 'required',
            'email' => 'required',
            'role' => 'nullable',
            'phone' => 'required',
            'gender' => 'required',
            'nrc_number' => 'required',
            'department_id' => 'required',
            'photo_path' => 'nullable',
        ];
    }
}

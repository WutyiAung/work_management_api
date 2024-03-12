<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
        $rules = [
            "name" => "required",
            "customer_id" => "required",
            "description" => "required",
            "value" => "required",
            "contract_date" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "user_id" => "required",
        ];

        // If document field is present and not null, validate it as a PDF
        if ($this->hasFile('document')) {
            $rules['document'] = 'nullable|file|mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:2048';
        }
        return $rules;
    }
}

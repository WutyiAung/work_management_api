<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesignRequest extends FormRequest
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
            "brand" => "required",
            "type_of_media" => "required",
            "deadline" => "required",
            "content_writer_id" => "required",
            "designer_id" => "required",
            "visual_copy" => "required",
            "headline" => "required",
            "body" => "required",
            "objective" => "required",
            "important_info" => "required",
            "taste_style" => "required",
            "reference" => "required",
            "reference_photo" => "nullable",
        ];
    }
}

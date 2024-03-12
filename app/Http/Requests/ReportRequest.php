<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
            "project_id" => "required",
            "customer_id" => "required",
            "user_id" => "required",
            "assigned_task_id" => "required",
            "status" => "nullable",
            "progress" => "nullable",
            "progress_description" => "nullable",
            "report_date" => "required",
            "report_time" => "required",
            "attachment_path" => "nullable",
            "photo_path" => "nullable",
            "video_path" => "nullable",
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentApplicationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => 'nullable|exists:advisor_db.courses,id',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'country' => 'nullable|string|max:255',
            'class_of_degree' => 'nullable|string|max:255',
            'schools_of_choice' => 'nullable|array',
            'country_of_preference' => 'nullable|array',
            'additional_notes' => 'nullable|string',
            'signature' => 'nullable|string',
            'application_documents' => 'nullable|array',
            'application_documents.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ];
    }
}

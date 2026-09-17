<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateStudentApplicationRequest extends FormRequest
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
            'email' => 'required|email',
            'course_id' => 'nullable|exists:advisor_db.courses,id',
            'course_name' => 'nullable|string',
            'class_of_degree' => 'required|string|max:255',
            'schools_of_choice' => 'required|array',
            'country_of_preference' => 'required|array',
            'additional_notes' => 'nullable|string',
            'signature' => 'nullable|string',
            'application_documents' => 'nullable|array',
            'application_documents.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'course_id.exists' => 'Please select a valid course',
            'class_of_degree.required' => 'Class of degree is required',
            'class_of_degree.string' => 'Class of degree must be a string',
            'class_of_degree.max' => 'Class of degree must not exceed 255 characters',
            'schools_of_choice.required' => 'Schools of choice is required',
            'schools_of_choice.array' => 'Schools of choice must be an array',
            'country_of_preference.required' => 'Country of preference is required',
            'country_of_preference.array' => 'Country of preference must be an array',
            'additional_notes.string' => 'Additional notes must be a string',
            'application_documents.array' => 'Application documents must be an array',
            'application_documents.*.file' => 'Each application document must be a file',
            'application_documents.*.uploaded' => 'The file failed to upload. The server accepts files up to ' . ini_get('upload_max_filesize') . ' each',
            'application_documents.*.mimes' => 'Application documents must be a file of type: jpg, jpeg, png, pdf, doc, docx',
            'application_documents.*.max' => 'Each application document must not exceed 10MB in size',
        ];
    }
}

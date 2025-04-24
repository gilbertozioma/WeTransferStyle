<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1', 'max:5'],
            'files.*' => [
                'required',
                'file',
                'max:102400', // 100MB in kilobytes
                'mimes:jpg,png,pdf,docx,zip',
            ],
            'expires_in' => ['sometimes', 'integer', 'min:1', 'max:30'],
            'email_to_notify' => ['sometimes', 'email'],
            'password' => ['sometimes', 'string', 'min:4'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'files' => 'files',
            'files.*' => 'file',
            'expires_in' => 'expiration period',
            'email_to_notify' => 'notification email',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'files.required' => 'At least one file is required.',
            'files.max' => 'You can upload a maximum of 5 files.',
            'files.*.max' => 'Each file cannot exceed 100MB.',
            'files.*.mimes' => 'Only jpg, png, pdf, docx, and zip files are allowed.',
        ];
    }
}

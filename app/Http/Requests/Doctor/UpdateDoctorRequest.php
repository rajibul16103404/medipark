<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
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
        $doctorId = $this->route('doctor')?->id;

        return [
            'doctor_name' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'department' => ['sometimes', 'nullable', 'string', 'max:255'],
            'specialist' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email_address' => ['sometimes', 'required', 'string', 'email:rfc', 'max:255', Rule::unique('doctors', 'email_address')->ignore($doctorId)],
            'mobile_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'gender' => ['sometimes', 'nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'known_languages' => ['sometimes', 'nullable', 'array'],
            'known_languages.*' => ['string', 'max:100'],
            'education' => ['sometimes', 'nullable', 'array'],
            'experience' => ['sometimes', 'nullable', 'array'],
            'social_media' => ['sometimes', 'nullable', 'array'],
            'membership' => ['sometimes', 'nullable', 'array'],
            'awards' => ['sometimes', 'nullable', 'array'],
            'registration_number' => ['sometimes', 'nullable', 'string', 'max:255'],
            'about' => ['sometimes', 'nullable', 'string'],
            'image' => $this->imageOrUrlRule(),
            'present_address' => ['sometimes', 'nullable', 'string'],
            'permanent_address' => ['sometimes', 'nullable', 'string'],
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
            'doctor_name.required' => 'The doctor name field is required.',
            'doctor_name.min' => 'The doctor name must be at least 2 characters.',
            'email_address.required' => 'The email address field is required.',
            'email_address.email' => 'The email address must be a valid email.',
            'email_address.unique' => 'The email address has already been taken.',
            'date_of_birth.before' => 'The date of birth must be a date before today.',
        ];
    }

    /**
     * Image or URL validation rule.
     *
     * @return array<int, mixed>
     */
    private function imageOrUrlRule(): array
    {
        return [
            'sometimes',
            'nullable',
            function (string $attribute, mixed $value, $fail): void {
                // Allow string URLs
                if (is_string($value) && ! $this->hasFile($attribute)) {
                    if (strlen($value) > 500) {
                        $fail('The '.$attribute.' URL must not exceed 500 characters.');
                    }

                    return;
                }

                // If it's a file upload, validate it
                if ($this->hasFile($attribute)) {
                    $file = $this->file($attribute);

                    // Check if file upload was successful
                    if (! $file->isValid()) {
                        $errorCode = $file->getError();
                        $errorMessages = [
                            UPLOAD_ERR_INI_SIZE => 'The file exceeds the upload_max_filesize directive in php.ini (currently '.ini_get('upload_max_filesize').'). Please increase upload_max_filesize and post_max_size in php.ini to at least 20MB for images.',
                            UPLOAD_ERR_FORM_SIZE => 'The file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                            UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded.',
                            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                        ];

                        $errorMessage = $errorMessages[$errorCode] ?? 'The '.$attribute.' failed to upload. Please check your file and try again.';
                        $fail($errorMessage);

                        return;
                    }

                    $mimeType = $file->getMimeType();
                    $size = $file->getSize();

                    // Check if it's an image
                    if (str_starts_with($mimeType, 'image/')) {
                        // Validate image size (20MB = 20971520 bytes)
                        if ($size > 20971520) {
                            $fail('The '.$attribute.' must not be larger than 20MB.');

                            return;
                        }

                        // Validate image type
                        $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
                        if (! in_array($mimeType, $allowedImageTypes, true)) {
                            $fail('The '.$attribute.' must be a valid image file (JPEG, PNG, GIF, WebP, or SVG).');

                            return;
                        }
                    } else {
                        $fail('The '.$attribute.' must be a valid image file (max 20mb) or a string URL.');
                    }
                } else {
                    $fail('The '.$attribute.' must be a valid image file (max 20mb) or a string URL.');
                }
            },
        ];
    }
}

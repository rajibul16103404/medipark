<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'present_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'blood_group' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'joining_date' => ['nullable', 'date'],
            'image' => $this->imageOrUrlRule(),
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
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
            'name.required' => 'The staff name field is required.',
            'name.min' => 'The staff name must be at least 2 characters.',
            'email.required' => 'The email address field is required.',
            'email.email' => 'The email address must be a valid email.',
            'email.unique' => 'The email address has already been taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'date_of_birth.before' => 'The date of birth must be a date before today.',
            'role_ids.*.exists' => 'One or more selected roles are invalid.',
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

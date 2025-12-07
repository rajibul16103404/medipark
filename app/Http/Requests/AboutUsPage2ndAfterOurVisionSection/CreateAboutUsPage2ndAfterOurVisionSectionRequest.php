<?php

namespace App\Http\Requests\AboutUsPage2ndAfterOurVisionSection;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAboutUsPage2ndAfterOurVisionSectionRequest extends FormRequest
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
        $imageValidation = function ($attribute, $value, $fail) {
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
                        UPLOAD_ERR_INI_SIZE => 'The file exceeds the upload_max_filesize directive in php.ini (currently 2MB). Please increase upload_max_filesize and post_max_size in php.ini to at least 20MB for images.',
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
                    if (! in_array($mimeType, $allowedImageTypes)) {
                        $fail('The '.$attribute.' must be a valid image file (JPEG, PNG, GIF, WebP, or SVG).');

                        return;
                    }
                } else {
                    $fail('The '.$attribute.' must be a valid image file (max 20mb) or a string URL.');
                }
            } elseif ($value !== null) {
                // If not a file and not a string, it's invalid
                $fail('The '.$attribute.' must be a valid image file (max 20mb) or a string URL.');
            }
        };

        return [
            'title' => ['nullable', 'string', 'max:255'],
            'paragraph' => ['nullable', 'string'],
            'image' => ['nullable', $imageValidation],
            'status' => ['nullable', Rule::enum(Status::class)],
        ];
    }
}

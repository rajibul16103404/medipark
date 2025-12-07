<?php

namespace App\Http\Requests\HomepageHeroSection;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHomepageHeroSectionRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'subtitle' => ['sometimes', 'string', 'max:255'],
            'background_image' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    // Allow string URLs
                    if (is_string($value) && ! $this->hasFile($attribute)) {
                        if (strlen($value) > 500) {
                            $fail('The background image URL must not exceed 500 characters.');
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
                                UPLOAD_ERR_INI_SIZE => 'The file exceeds the upload_max_filesize directive in php.ini (currently 2MB). Please increase upload_max_filesize and post_max_size in php.ini to at least 50MB for videos.',
                                UPLOAD_ERR_FORM_SIZE => 'The file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                                UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded.',
                                UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                            ];

                            $errorMessage = $errorMessages[$errorCode] ?? 'The background image failed to upload. Please check your file and try again.';
                            $fail($errorMessage);

                            return;
                        }

                        $mimeType = $file->getMimeType();
                        $size = $file->getSize();

                        // Check if it's an image
                        if (str_starts_with($mimeType, 'image/')) {
                            // Validate image size (20MB = 20971520 bytes)
                            if ($size > 20971520) {
                                $fail('The background image must not be larger than 20MB.');

                                return;
                            }

                            // Validate image type
                            $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
                            if (! in_array($mimeType, $allowedImageTypes)) {
                                $fail('The background image must be a valid image file (JPEG, PNG, GIF, WebP, or SVG).');

                                return;
                            }
                        } elseif (str_starts_with($mimeType, 'video/')) {
                            // Validate video size (50MB = 52428800 bytes)
                            if ($size > 52428800) {
                                $fail('The background video must not be larger than 50MB.');

                                return;
                            }

                            // Validate video type
                            $allowedVideoTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/x-flv', 'video/webm'];
                            if (! in_array($mimeType, $allowedVideoTypes)) {
                                $fail('The background video must be a valid video file (MP4, MOV, AVI, WMV, FLV, or WebM).');

                                return;
                            }
                        } else {
                            $fail('The background image must be a valid image file (max 20mb), video file (max 50mb), or a string URL.');
                        }
                    } elseif ($value !== null) {
                        // If not a file and not a string, it's invalid
                        $fail('The background image must be a valid image file (max 20mb), video file (max 50mb), or a string URL.');
                    }
                },
            ],
            'opacity' => ['sometimes', 'string', 'max:255'],
            'serial' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', Rule::enum(Status::class)],
        ];
    }
}

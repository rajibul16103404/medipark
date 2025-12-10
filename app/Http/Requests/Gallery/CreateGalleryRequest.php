<?php

namespace App\Http\Requests\Gallery;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGalleryRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'image' => $this->imageOrUrlRule(),
            'status' => ['nullable', Rule::enum(Status::class)],
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
                if (is_string($value) && ! $this->hasFile($attribute)) {
                    if (strlen($value) > 500) {
                        $fail('The '.$attribute.' URL must not exceed 500 characters.');
                    }

                    return;
                }

                if ($this->hasFile($attribute)) {
                    $file = $this->file($attribute);

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

                    if (str_starts_with($mimeType, 'image/')) {
                        if ($size > 20971520) {
                            $fail('The '.$attribute.' must not be larger than 20MB.');

                            return;
                        }

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

<?php

namespace App\Http\Requests\VideoLink;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateVideoLinkRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:5000'],
            'video' => $this->videoOrUrlRule(),
            'status' => ['nullable', Rule::enum(Status::class)],
        ];
    }

    /**
     * Video or URL validation rule.
     *
     * @return array<int, mixed>
     */
    private function videoOrUrlRule(): array
    {
        return [
            'nullable',
            function (string $attribute, mixed $value, $fail): void {
                // Allow string URLs
                if (is_string($value) && ! $this->hasFile($attribute)) {
                    if (strlen($value) > 1000) {
                        $fail('The '.$attribute.' URL must not exceed 1000 characters.');
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
                            UPLOAD_ERR_INI_SIZE => 'The file exceeds the upload_max_filesize directive in php.ini (currently '.ini_get('upload_max_filesize').'). Please increase upload_max_filesize and post_max_size in php.ini to at least 100MB for videos.',
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

                    // Check if it's a video - accept all video types
                    if (str_starts_with($mimeType, 'video/')) {
                        // Validate video size (100MB = 104857600 bytes)
                        if ($size > 104857600) {
                            $fail('The '.$attribute.' must not be larger than 100MB.');

                            return;
                        }
                    } else {
                        $fail('The '.$attribute.' must be a valid video file (max 100MB) or a string URL.');
                    }
                } else {
                    $fail('The '.$attribute.' must be a valid video file (max 100MB) or a string URL.');
                }
            },
        ];
    }
}

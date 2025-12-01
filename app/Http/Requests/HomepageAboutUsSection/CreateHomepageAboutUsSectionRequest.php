<?php

namespace App\Http\Requests\HomepageAboutUsSection;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class CreateHomepageAboutUsSectionRequest extends FormRequest
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
            if ($this->hasFile($attribute)) {
                $file = $this->file($attribute);
                $validator = \Illuminate\Support\Facades\Validator::make(
                    [$attribute => $file],
                    [$attribute => [File::image(allowSvg: true)->max('20mb')]]
                );
                if ($validator->fails()) {
                    $fail($validator->errors()->first($attribute));
                }
            } elseif (is_string($value) && strlen($value) > 500) {
                $fail('The '.$attribute.' URL must not exceed 500 characters.');
            } elseif ($value !== null && ! is_string($value) && ! $this->hasFile($attribute)) {
                $fail('The '.$attribute.' must be a valid image file (max 20mb) or a string URL.');
            }
        };

        return [
            'title' => ['nullable', 'string', 'max:255'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'image_1' => ['nullable', $imageValidation],
            'image_2' => ['nullable', $imageValidation],
            'image_3' => ['nullable', $imageValidation],
            'status' => ['nullable', Rule::enum(Status::class)],
        ];
    }
}

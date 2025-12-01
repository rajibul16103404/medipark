<?php

namespace App\Http\Requests\HomepageHeroSection;

use App\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class CreateHomepageHeroSectionRequest extends FormRequest
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
            'subtitle' => ['required', 'string', 'max:255'],
            'background_image' => [
                'required',
                function ($attribute, $value, $fail) {
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
                        $fail('The background image URL must not exceed 500 characters.');
                    } elseif ($value !== null && ! is_string($value) && ! $this->hasFile($attribute)) {
                        $fail('The background image must be a valid image file (max 20mb) or a string URL.');
                    }
                },
            ],
            'opacity' => ['required', 'string', 'max:255'],
            'serial' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::enum(Status::class)],
        ];
    }
}

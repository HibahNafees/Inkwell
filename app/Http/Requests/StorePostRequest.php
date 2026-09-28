<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'body' => ['required', 'string', 'max:100000'],
            'tags' => ['nullable', 'string', 'max:200'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048', 'dimensions:max_width=6000,max_height=6000'],
            'remove_cover' => ['nullable', 'boolean'],
            'action' => ['required', 'in:draft,publish'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cover.max' => 'The cover image may not be larger than 2 MB.',
            'cover.uploaded' => 'The cover image failed to upload — it may be larger than 2 MB.',
        ];
    }
}

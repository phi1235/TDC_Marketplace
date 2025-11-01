<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', 'in:text,image'],
            'content' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
            'image' => ['nullable', 'image', 'max:5120'], // 5MB max
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Loại tin nhắn phải là text hoặc image.',
            'image.image' => 'File phải là hình ảnh.',
            'image.max' => 'Kích thước ảnh không được vượt quá 5MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Ensure content or image is provided
        if (!$this->has('content') && !$this->hasFile('image')) {
            $this->merge(['content' => '']);
        }
    }
}


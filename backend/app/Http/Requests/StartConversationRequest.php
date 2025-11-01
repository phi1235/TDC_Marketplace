<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'is_support' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'ID người dùng là bắt buộc.',
            'user_id.integer' => 'ID người dùng phải là số nguyên.',
            'user_id.exists' => 'Người dùng không tồn tại.',
            'is_support.boolean' => 'Giá trị is_support phải là boolean.',
        ];
    }
}


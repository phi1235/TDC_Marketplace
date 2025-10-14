<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập đầy đủ email và mật khẩu.',
            'email.email' => 'Email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 150 ký tự.',
            'password.required' => 'Vui lòng nhập đầy đủ email và mật khẩu.',
            'password.min' => 'Mật khẩu quá ngắn, tối thiểu 6 ký tự.',
            'password.max' => 'Mật khẩu quá dài.',
        ];
    }
}

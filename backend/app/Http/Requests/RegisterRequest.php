<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.max' => 'Họ tên không được vượt quá 100 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ email hợp lệ.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            'email.max' => 'Email không được vượt quá 150 ký tự.',
            'password.required' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.min' => 'Mật khẩu quá ngắn, tối thiểu 6 ký tự.',
            'password.max' => 'Mật khẩu quá dài.',
        ];
    }
}

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
            // Name: 2-100 ký tự, chỉ cho phép chữ (Unicode), khoảng trắng, dấu ', ., -
            // Regex chuẩn (escape đầy đủ cho PHP single-quoted string)
            'name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\'\.\-]+$/u'],
            // Stricter email validation: RFC + DNS
            'email' => ['required', 'email:rfc,dns', 'max:150', 'unique:users,email'],
            // Require confirmation using Laravel's confirmed rule (expects password_confirmation)
            'password' => ['required', 'string', 'min:6', 'max:100', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.min' => 'Họ tên phải có ít nhất 2 ký tự.',
            'name.max' => 'Họ tên không được vượt quá 100 ký tự.',
            'name.regex' => "Họ tên chỉ được chứa chữ cái, khoảng trắng và các ký tự '.-.",
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            'email.max' => 'Email không được vượt quá 150 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu quá ngắn, tối thiểu 6 ký tự.',
            'password.max' => 'Mật khẩu quá dài.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password_confirmation.required' => 'Vui lòng nhập lại mật khẩu để xác nhận.',
        ];
    }
}

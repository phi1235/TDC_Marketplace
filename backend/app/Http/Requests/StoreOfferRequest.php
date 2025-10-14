<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offer_price' => ['required', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'offer_price.required' => 'Vui lòng nhập giá đề nghị.',
            'offer_price.numeric' => 'Giá đề nghị phải là số.',
            'offer_price.min' => 'Giá đề nghị phải lớn hơn 0.',
            'message.max' => 'Tin nhắn không được vượt quá 500 ký tự.',
        ];
    }
}

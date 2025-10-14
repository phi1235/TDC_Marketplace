<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'max:200'],
            'description' => ['sometimes', 'string', 'max:2000'],
            'condition_grade' => ['sometimes', 'in:A,B,C,D'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'in:VND,USD'],
            'images' => ['sometimes', 'array', 'max:5'],
            'images.*' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Danh mục không tồn tại.',
            'title.max' => 'Tiêu đề không được vượt quá 200 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 2000 ký tự.',
            'condition_grade.in' => 'Tình trạng sản phẩm không hợp lệ.',
            'price.numeric' => 'Giá phải là số.',
            'price.min' => 'Giá phải lớn hơn 0.',
            'original_price.numeric' => 'Giá gốc phải là số.',
            'original_price.min' => 'Giá gốc phải lớn hơn 0.',
            'currency.in' => 'Loại tiền tệ không hợp lệ.',
            'images.array' => 'Ảnh phải là mảng.',
            'images.max' => 'Bạn chỉ được tải lên tối đa 5 hình ảnh.',
            'images.*.image' => 'File phải là ảnh.',
            'images.*.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg hoặc webp.',
            'images.*.max' => 'Ảnh không được vượt quá 5MB.',
        ];
    }
}

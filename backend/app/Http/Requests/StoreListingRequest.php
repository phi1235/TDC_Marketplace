<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:2000'],
            'condition' => ['required', 'string', 'in:excellent,good,fair,poor'],
            'price' => ['required', 'numeric', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'images' => ['sometimes', 'array', 'max:5'],
            'images.*' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Vui lòng chọn danh mục đăng tin.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'title.required' => 'Vui lòng nhập tiêu đề bài đăng.',
            'title.max' => 'Tiêu đề không được vượt quá 200 ký tự.',
            'description.required' => 'Vui lòng nhập mô tả chi tiết cho sản phẩm.',
            'description.max' => 'Mô tả không được vượt quá 2000 ký tự.',
            'condition.required' => 'Vui lòng chọn tình trạng sản phẩm.',
            'condition.in' => 'Tình trạng sản phẩm không hợp lệ.',
            'price.required' => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric' => 'Giá phải là số.',
            'price.min' => 'Giá phải lớn hơn 0.',
            'location.max' => 'Địa điểm không được vượt quá 255 ký tự.',
            'images.required' => 'Vui lòng chọn ít nhất một ảnh.',
            'images.array' => 'Ảnh phải là mảng.',
            'images.min' => 'Vui lòng chọn ít nhất một ảnh.',
            'images.max' => 'Bạn chỉ được tải lên tối đa 5 hình ảnh.',
            'images.*.required' => 'Vui lòng chọn ảnh.',
            'images.*.image' => 'File phải là ảnh.',
            'images.*.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg hoặc webp.',
            'images.*.max' => 'Ảnh không được vượt quá 5MB.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateReportRequest extends FormRequest
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
            'reportable_type' => [
                'required',
                'string',
                Rule::in([
                    'App\\Models\\Listing',
                    'App\\Models\\User', 
                    'App\\Models\\Review'
                ])
            ],
            'reportable_id' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $reportableType = $this->input('reportable_type');
                    if ($reportableType && !class_exists($reportableType)) {
                        $fail('Loại đối tượng báo cáo không hợp lệ.');
                        return;
                    }
                    
                    if ($reportableType && !$reportableType::find($value)) {
                        $fail('Đối tượng báo cáo không tồn tại.');
                    }
                }
            ],
            'reason' => [
                'required',
                'string',
                Rule::in([
                    'fraud',
                    'fake_product', 
                    'spam',
                    'inappropriate_content',
                    'price_manipulation',
                    'fake_reviews',
                    'harassment',
                    'copyright_violation',
                    'other'
                ])
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'reportable_type.required' => 'Vui lòng chọn loại đối tượng báo cáo.',
            'reportable_type.in' => 'Loại đối tượng báo cáo không hợp lệ.',
            'reportable_id.required' => 'Vui lòng chọn đối tượng cần báo cáo.',
            'reportable_id.integer' => 'ID đối tượng báo cáo phải là số.',
            'reportable_id.min' => 'ID đối tượng báo cáo không hợp lệ.',
            'reason.required' => 'Vui lòng chọn lý do báo cáo.',
            'reason.in' => 'Lý do báo cáo không hợp lệ.',
            'description.required' => 'Vui lòng mô tả chi tiết về vấn đề.',
            'description.min' => 'Mô tả phải có ít nhất 10 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'reportable_type' => 'loại đối tượng báo cáo',
            'reportable_id' => 'đối tượng báo cáo',
            'reason' => 'lý do báo cáo',
            'description' => 'mô tả chi tiết',
        ];
    }
}

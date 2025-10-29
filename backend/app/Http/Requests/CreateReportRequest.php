<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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
                function ($attribute, $value, $fail) {
                    // Normalize and validate strictly against allowed model classes
                    $normalized = str_replace('\\\\', '\\', $value);
                    $allowed = [
                        'App\\Models\\Listing',
                        'App\\Models\\User',
                        'App\\Models\\Review',
                    ];

                    if (!in_array($normalized, $allowed, true)) {
                        $fail('Loại đối tượng báo cáo không hợp lệ.');
                    }
                }
            ],
            'reportable_id' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $inputType = str_replace('\\\\', '\\', (string) $this->input('reportable_type'));

                    // Use table-level validation to avoid touching Eloquent models (prevents autoload redeclare issues)
                    $map = [
                        'App\\Models\\Listing' => ['table' => 'listings', 'owner' => 'seller_id'],
                        'App\\Models\\User' => ['table' => 'users', 'owner' => 'id'],
                        'App\\Models\\Review' => ['table' => 'reviews', 'owner' => 'user_id'],
                    ];

                    if (!isset($map[$inputType])) {
                        $fail('Loại đối tượng báo cáo không hợp lệ.');
                        return;
                    }

                    $table = $map[$inputType]['table'];
                    $ownerColumn = $map[$inputType]['owner'];
                    $reportable = DB::table($table)->where('id', $value)->first();
                    if (!$reportable) {
                        $fail('Đối tượng báo cáo không tồn tại.');
                        return;
                    }
                    
                    // Kiểm tra nếu user đang báo cáo chính mình
                    $userId = auth()->id();
                    if (!$userId) {
                        $fail('Bạn cần đăng nhập để báo cáo.');
                        return;
                    }
                    
                    // Nếu đối tượng báo cáo là User, check owner
                    if ($table === 'users') {
                        if ($reportable->id === $userId) {
                            $fail('Bạn không thể báo cáo chính mình.');
                        }
                    } elseif ($table === 'listings') {
                        // Nếu đối tượng báo cáo là Listing, check listing owner
                        if ((int) ($reportable->$ownerColumn ?? 0) === (int) $userId) {
                            $fail('Bạn không thể báo cáo bài đăng của chính mình.');
                        }
                    } elseif ($table === 'reviews') {
                        // Nếu đối tượng báo cáo là Review, check review owner
                        if ($reportable->user_id === $userId) {
                            $fail('Bạn không thể báo cáo đánh giá của chính mình.');
                        }
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

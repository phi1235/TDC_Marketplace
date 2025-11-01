<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KeywordModeration
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Lấy toàn bộ blocklist từ config/keyword_blocklist.php
        $categories = config('keyword_blocklist', []);

        // Gom tất cả keyword thành 1 mảng phẳng, unique
        $blockedWords = collect($categories)
            ->flatten()
            ->filter(fn ($w) => !empty($w))
            ->unique()
            ->values()
            ->all();

        // 2. Gom toàn bộ text user gửi lên (trừ field file)
        $textParts = [];

        foreach ($request->except(['images', 'files', 'file', 'photos']) as $key => $value) {
            if (is_array($value)) {
                $textParts[] = implode(' ', $value);
            } else {
                $textParts[] = (string) $value;
            }
        }

        // Chuỗi tổng
        $combinedOriginal = implode(' ', $textParts);

        // Chuẩn hóa để so sánh:
        //  - lowercase
        //  - bỏ dấu câu/cách phổ biến
        //  - đưa về 1 dòng
        $normalizedText = Str::lower($combinedOriginal);
        $normalizedText = str_replace(
            ["\n", "\r", "\t"],
            ' ',
            $normalizedText
        );
        // Bỏ bớt các ký tự người ta hay chèn để lách (., -, _, khoảng trắng liền nhau)
        $squashText = str_replace(['.', '-', '_'], '', $normalizedText);
        // ép nhiều khoảng trắng -> 1 khoảng trắng
        $squashText = preg_replace('/\s+/', ' ', $squashText);

        $detected = [];

        foreach ($blockedWords as $word) {
            $wordNorm = Str::lower($word);
            // bỏ ký tự lách tương tự
            $wordNormSquash = str_replace(['.', '-', '_'], '', $wordNorm);

            // Nếu chuỗi chứa nguyên cụm cấm thì bắt
            // ví dụ:
            //  - text: "sex"
            //  - word: "sex"
            //  - hoặc "z.a.l.o" vs "zalo"
            if (
                Str::contains($squashText, $wordNormSquash)
            ) {
                $detected[] = $word;
            }
        }

        // Xử lý kết quả
        if (!empty($detected)) {
            $detected = array_values(array_unique($detected));

            $message = "🚫 Nội dung chứa các từ khóa bị cấm: " . implode(', ', $detected) . ". Vui lòng chỉnh sửa lại.";

            Log::warning('🚫 Blocked keywords detected', [
                'words' => $detected,
                'path' => $request->path(),
                'user_id' => $request->user()->id ?? null,
                'payload_preview' => Str::limit($combinedOriginal, 200),
            ]);

            return response()->json([
                'success' => false,
                'message' => $message,
                'blocked_words' => $detected,
            ], 400);
        }

        return $next($request);
    }
}

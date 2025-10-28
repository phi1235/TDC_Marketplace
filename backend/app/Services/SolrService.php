<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolrService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SOLR_URL', 'http://solr:8983/solr/listings');
    }

    /**
     *  Index (hoặc cập nhật) document vào Solr
     */
    public function indexDocument(array $data): bool
    {
        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/update?commit=true", [$data]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Solr indexDocument failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     *  Tìm kiếm cơ bản theo title + description
     */
    public function search(string $q)
    {
        $url = rtrim($this->baseUrl, '/') . '/select';

        $params = [
            'defType' => 'edismax',        // Parser thông minh
            'q' => "title:($q* OR $q~0.8)", // Prefix + fuzzy
            'qf' => 'title^3',             // Ưu tiên cao cho title
            'fl' => 'id,title,price,image,description,score', // Field cần hiển thị
            'rows' => 30,
            'wt' => 'json',
            'mm' => '80%',                 // Ít nhất phải khớp 80%
            'hl' => 'true',
            'hl.fl' => 'title',
            'hl.simple.pre' => '<mark>',
            'hl.simple.post' => '</mark>',
        ];

        try {
            $response = Http::get($url, $params);
            if ($response->failed()) {
                Log::error('Solr search failed', ['response' => $response->body()]);
                return [];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SolrService search exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     *  Xoá tất cả dữ liệu trong core listings
     */
    public function clear(): bool
    {
        try {
            $query = ['delete' => ['query' => '*:*']];
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/update?commit=true", $query);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Solr clear failed: ' . $e->getMessage());
            return false;
        }
    }
}

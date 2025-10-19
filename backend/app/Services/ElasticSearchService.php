<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElasticSearchService
{
    protected string $baseUrl;

    public function __construct()
    {
        // ⚙️ Dùng service name trong docker-compose (không phải localhost)
        $this->baseUrl = env('ELASTICSEARCH_URL', 'http://elasticsearch:9200');
    }

    /**
     * 🧩 Index (hoặc cập nhật) một document vào Elasticsearch
     */
    public function indexDocument(string $index, int|string $id, array $data): bool
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->put("{$this->baseUrl}/{$index}/_doc/{$id}", $data);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Elasticsearch indexDocument failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔍 Tìm kiếm cơ bản (theo name + description)
     */
    public function search(string $index, string $query): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/{$index}/_search", [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['name^3', 'description'],
                    ],
                ],
            ]);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Elasticsearch search failed: ' . $e->getMessage());
            return ['hits' => ['hits' => []]];
        }
    }

    /**
     * ⚡ Tìm kiếm nâng cao (cho phép bool_prefix, fuzzy, 1 ký tự)
     */
    public function customSearch(string $index, array $query): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/{$index}/_search", $query);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Elasticsearch customSearch failed: ' . $e->getMessage());
            return ['hits' => ['hits' => []]];
        }
    }

    /**
     * 🧹 Xoá toàn bộ index (dùng khi reset)
     */
    public function deleteIndex(string $index): bool
    {
        $response = Http::delete("{$this->baseUrl}/{$index}");
        return $response->successful();
    }

    /**
     * 🏗️ Tạo lại index với cấu hình analyzer hỗ trợ tiếng Việt
     */
    public function recreateIndex(string $index): bool
    {
        $this->deleteIndex($index);

        $settings = [
            'settings' => [
                'analysis' => [
                    'tokenizer' => [
                        'my_ngram_tokenizer' => [
                            'type' => 'ngram',
                            'min_gram' => 1,
                            'max_gram' => 15,
                            'token_chars' => ['letter', 'digit']
                        ]
                    ],
                    'filter' => [
                        'vn_normalizer' => [
                            'type' => 'asciifolding',
                            'preserve_original' => true
                        ]
                    ],
                    'analyzer' => [
                        'vn_analyzer' => [
                            'tokenizer' => 'my_ngram_tokenizer',
                            'filter' => ['lowercase', 'vn_normalizer']
                        ],
                        'vn_search' => [
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase', 'vn_normalizer']
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'vn_analyzer',
                        'search_analyzer' => 'vn_search'
                    ],
                    'price' => ['type' => 'float'],
                    'category' => ['type' => 'integer']
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->put("{$this->baseUrl}/{$index}", $settings);

        return $response->successful();
    }
}

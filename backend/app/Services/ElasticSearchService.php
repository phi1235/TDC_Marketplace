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
                        'fields' => ['title^3', 'description'],
                        'type' => 'best_fields',
                        'fuzziness' => 'AUTO',
                        'minimum_should_match' => '70%',
                    ]
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
            if (!isset($query['query']['bool'])) {
                $query['query']['bool'] = [];
            }

            if (!isset($query['query']['bool']['must'])) {
                $query['query']['bool']['must'] = [];
            }

            if (isset($query['query']['bool']['should'])) {
                $keyword = $query['query']['bool']['should'][0]['match']['title']['query'] ?? null;

                if ($keyword) {
                    $query['query']['bool']['must'][] = [
                        'multi_match' => [
                            'query' => $keyword,
                            'fields' => ['title^3', 'description'],
                            'minimum_should_match' => '30%',
                        ]
                    ];
                }
            }

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
     * 🗑️ Xoá một document cụ thể
     */
    public function deleteDocument(string $index, int|string $id): bool
    {
        try {
            $response = Http::delete("{$this->baseUrl}/{$index}/_doc/{$id}");
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Elasticsearch deleteDocument failed: ' . $e->getMessage());
            return false;
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
        // Xóa index cũ nếu có
        $this->deleteIndex($index);
        sleep(2); // đợi 2 giây để ES xóa hoàn tất

        $settings = [
            'settings' => [
                'analysis' => [
                    'analyzer' => [
                        'vn_analyzer' => [
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase', 'asciifolding']
                        ],
                        'vn_search' => [
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase', 'asciifolding']
                        ]
                    ]
                ]
            ],
            'mappings' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'vn_analyzer',
                        'search_analyzer' => 'vn_search'
                    ],
                    'title_suggest' => [
                        'type' => 'completion',
                        'analyzer' => 'simple',
                        'preserve_separators' => true,
                        'preserve_position_increments' => true,
                        'max_input_length' => 50
                    ],
                    'description' => [
                        'type' => 'text',
                        'analyzer' => 'vn_analyzer',
                        'search_analyzer' => 'vn_search'
                    ],
                    'price' => ['type' => 'float'],
                    'category_id' => ['type' => 'integer'],
                    'image' => ['type' => 'keyword'],
                    'created_at' => ['type' => 'date'],
                ],
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->put("{$this->baseUrl}/{$index}", $settings);

        Log::info('Elastic recreateIndex response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return $response->ok() || $response->status() === 400;
    }
    public function logSearch(string $keyword, int $resultsCount, ?int $userId = null): bool
    {
        try {
            $payload = [
                'user_id' => ($userId ?? 0),
                'keyword' => $keyword,
                'timestamp' => now()->toISOString(),
                'results_count' => $resultsCount,
            ];

            $res = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/search_history/_doc", $payload);

            return $res->successful();
        } catch (\Throwable $e) {
            Log::error('Elasticsearch logSearch failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo lại index search_history (mapping đơn giản cho thống kê & truy vấn theo user).
     */
    public function recreateHistoryIndex(): bool
    {
        // Xoá trước (nếu có)
        try {
            Http::delete("{$this->baseUrl}/search_history");
        } catch (\Throwable $e) {
        }
        usleep(300000); // 0.3s

        $body = [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
            'mappings' => [
                'properties' => [
                    'user_id'       => ['type' => 'keyword'],
                    'keyword'       => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                    'timestamp'     => ['type' => 'date'],
                    'results_count' => ['type' => 'integer'],
                ]
            ]
        ];

        $res = Http::withHeaders(['Content-Type' => 'application/json'])
            ->put("{$this->baseUrl}/search_history", $body);

        Log::info('recreateHistoryIndex', ['status' => $res->status(), 'body' => $res->body()]);
        return $res->ok() || $res->status() === 400;
    }
    public function clearSearchHistoryByUser(?int $userId = null): bool
    {
        $id = (string)($userId ?? 0);
        $url = "{$this->baseUrl}/search_history/_delete_by_query?conflicts=proceed&refresh=wait_for";

        try {
            $body = [
                'query' => [
                    'term' => ['user_id' => $id]
                ]
            ];

            $res = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $body);

            \Illuminate\Support\Facades\Log::info('🔍 ES Delete', [
                'user_id' => $id,
                'url' => $url,
                'status' => $res->status(),
                'body' => $res->body()
            ]);

            return $res->successful();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('❌ ES clearSearchHistoryByUser failed: ' . $e->getMessage());
            return false;
        }
    }
    public function deleteByQuery(string $index, array $query): array
    {
        try {
            $url = "{$this->baseUrl}/{$index}/_delete_by_query?conflicts=proceed&refresh=true";
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, ['query' => $query]);

            Log::info('🔍 ES Delete', [
                'user_id' => auth()->id() ?? 0,
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('❌ Elasticsearch deleteByQuery failed: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}

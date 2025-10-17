<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ElasticSearchService
{
    protected $baseUrl;

    public function __construct()
    {
        // ⚠️ Trong Docker: dùng 'elasticsearch', không phải 'localhost'
        $this->baseUrl = env('ELASTICSEARCH_URL', 'http://elasticsearch:9200');
    }

    /**
     * Index (hoặc cập nhật) một document vào Elasticsearch.
     */
    public function indexDocument(string $index, $id, array $data): bool
    {
        $response = Http::put("{$this->baseUrl}/{$index}/_doc/{$id}", $data);
        return $response->successful();
    }

    /**
     * Tìm kiếm trong index Elasticsearch.
     */
    public function search(string $index, string $query): array
    {
        $response = Http::post("{$this->baseUrl}/{$index}/_search", [
            'query' => [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['name', 'description'],
                ],
            ],
        ]);

        return $response->json();
    }
}

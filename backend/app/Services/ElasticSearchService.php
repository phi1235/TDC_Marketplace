<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ElasticSearchService
{
    public function search(string $index, string $keyword = '')
    {
        $url = "http://elasticsearch:9200/{$index}/_search";

        $body = [
            "query" => [
                "multi_match" => [
                    "query" => $keyword,
                    "fields" => ["name", "description"]
                ]
            ]
        ];

        $response = Http::post($url, $body);

        if ($response->failed()) {
            return ['error' => 'Elasticsearch request failed'];
        }

        return $response->json();
    }
}

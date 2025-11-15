<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolrService
{
    protected string $core;
    protected string $baseUrl;

    public function __construct()
    {
        $this->core = env('SOLR_CORE', 'listings');
        $this->baseUrl = rtrim(env('SOLR_URL', 'http://solr:8983/solr'), '/');
    }

    /**
     * Index document
     */
    public function indexDocument(array $doc): bool
    {
        try {
            $url = "{$this->baseUrl}/{$this->core}/update?commit=true";

            $res = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [$doc]);

            return $res->successful();
        } catch (\Throwable $e) {
            Log::error("Solr indexDocument failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Smart Search – Tương đương với Elasticsearch customSearch()
     */
    public function smartSearch(string $q): array
    {
        $url = "{$this->baseUrl}/{$this->core}/select";

        /**
         * Tương đương Elasticsearch:
         * - bool_prefix → title:q*
         * - fuzziness AUTO → ~2~3
         * - match_phrase_prefix → pf + pf2
         * - qf boost: title^3, description
         * - mm = 1 giống minimum_should_match = 1
         */
        $params = [
            'defType' => 'edismax',

            // bool_prefix + fuzzy + phrase_prefix
            'q' => "(
                title:{$q}*^3
                OR title:{$q}~3^3
                OR description:{$q}*^2
                OR description:{$q}~3
            )",

            // Boost fields
            'qf' => 'title^3 description',
            'pf' => 'title^3',
            'pf2' => 'description',

            // Minimum match giống ES minimum_should_match = 1
            'mm' => '1',

            // Số lượng giống ES
            'rows' => 30,

            // Sort theo score như ES
            'sort' => 'score desc',

            'wt' => 'json',

            // Highlight giống ES
            'hl' => 'true',
            'hl.fl' => 'title,description',
            'hl.simple.pre' => '<mark>',
            'hl.simple.post' => '</mark>',
        ];

        try {
            $res = Http::get($url, $params);

            if ($res->failed()) {
                Log::error("Solr smartSearch failed", ['body' => $res->body()]);
                return [];
            }

            return $res->json();
        } catch (\Throwable $e) {
            Log::error("Solr smartSearch exception: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Suggestions (tương đương ES completion suggester)
     */
    public function suggest(string $q, int $limit = 10): array
    {
        try {
            $url = "{$this->baseUrl}/{$this->core}/suggest";

            $params = [
                'suggest.q' => $q,
                'wt' => 'json',
            ];

            $res = Http::get($url, $params)->json();

            $items = $res['suggest']['title_suggest'][$q]['suggestions'] ?? [];
            $terms = array_map(fn($i) => $i['term'], $items);

            return array_slice(array_unique($terms), 0, $limit);
        } catch (\Throwable $e) {
            Log::error("Solr suggest failed: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Delete by ID
     */
    public function deleteDocument(int|string $id): bool
    {
        try {
            $url = "{$this->baseUrl}/{$this->core}/update?commit=true";

            $payload = ['delete' => ['id' => (string)$id]];

            $res = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            return $res->successful();
        } catch (\Throwable $e) {
            Log::error("Solr deleteDocument failed: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Delete by query
     */
    public function deleteByQuery(string $query): bool
    {
        try {
            $url = "{$this->baseUrl}/{$this->core}/update?commit=true";

            $payload = ['delete' => ['query' => $query]];

            $res = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            return $res->successful();
        } catch (\Throwable $e) {
            Log::error("Solr deleteByQuery failed: {$e->getMessage()}");
            return false;
        }
    }

    public function clear(): bool
    {
        return $this->deleteByQuery('*:*');
    }

    /**
     * Search history (giống ES)
     */
    public function logSearch(string $keyword, int $count, ?int $userId = 0): bool
    {
        try {
            $url = "{$this->baseUrl}/search_history/update?commit=true";

            $payload = [[
                'id' => uniqid(),
                'user_id' => (string)($userId ?? 0),
                'keyword' => $keyword,
                'results_count' => $count,
                'timestamp' => now()->toISOString(),
            ]];

            $res = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            return $res->successful();
        } catch (\Throwable $e) {
            Log::error("Solr logSearch failed: {$e->getMessage()}");
            return false;
        }
    }

    public function getSearchHistory(int $userId): array
    {
        try {
            $url = "{$this->baseUrl}/search_history/select";

            $params = [
                'q' => "user_id:{$userId}",
                'sort' => 'timestamp desc',
                'rows' => 10,
                'wt' => 'json',
            ];

            $res = Http::get($url, $params)->json();

            return $res['response']['docs'] ?? [];
        } catch (\Throwable $e) {
            Log::error("Solr getSearchHistory failed: {$e->getMessage()}");
            return [];
        }
    }

    public function clearSearchHistoryByUser(int $userId): bool
    {
        return $this->deleteByQuery("user_id:{$userId}");
    }
}

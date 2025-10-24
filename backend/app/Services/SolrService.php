<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolrService
{
    protected string $baseUrl;
    protected string $core;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('solr.url', 'http://solr:8983');
        $this->core = config('solr.core', 'listings');
        $this->timeout = config('solr.timeout', 30);
    }

    /**
     * 🧩 Index (hoặc cập nhật) một document vào Solr
     */
    public function indexDocument(string $id, array $data): bool
    {
        try {
            $data['id'] = $id;
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->baseUrl}/solr/{$this->core}/update/json/docs", [$data]);

            if ($response->successful()) {
                // Commit changes
                $this->commit();
                return true;
            }

            Log::error('Solr indexDocument failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Solr indexDocument error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📦 Bulk index nhiều documents
     */
    public function bulkIndex(array $documents): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->baseUrl}/solr/{$this->core}/update/json/docs", $documents);

            if ($response->successful()) {
                $this->commit();
                return true;
            }

            Log::error('Solr bulkIndex failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Solr bulkIndex error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔍 Tìm kiếm cơ bản
     */
    public function search(string $query, array $params = []): array
    {
        try {
            $defaultParams = [
                'q' => $query,
                'wt' => 'json',
                'rows' => 30,
                'start' => 0,
            ];

            $searchParams = array_merge($defaultParams, $params);

            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/solr/{$this->core}/select", $searchParams);

            $result = $response->json();
            return $result ?: ['response' => ['docs' => [], 'numFound' => 0]];
        } catch (\Throwable $e) {
            Log::error('Solr search failed: ' . $e->getMessage());
            return ['response' => ['docs' => [], 'numFound' => 0]];
        }
    }

    /**
     * ⚡ Tìm kiếm nâng cao với query DSL
     */
    public function customSearch(array $query): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/solr/{$this->core}/select", $query);

            $result = $response->json();
            return $result ?: ['response' => ['docs' => [], 'numFound' => 0]];
        } catch (\Throwable $e) {
            Log::error('Solr customSearch failed: ' . $e->getMessage());
            return ['response' => ['docs' => [], 'numFound' => 0]];
        }
    }

    /**
     * 💡 Gợi ý autocomplete
     */
    public function suggest(string $query, int $limit = 10): array
    {
        try {
            $params = [
                'q' => $query,
                'suggest' => 'true',
                'suggest.dictionary' => 'mySuggester',
                'suggest.count' => $limit,
                'wt' => 'json',
            ];

            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/solr/{$this->core}/suggest", $params);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Solr suggest failed: ' . $e->getMessage());
            return ['suggest' => ['mySuggester' => ['suggestions' => []]]];
        }
    }

    /**
     * 🗑️ Xóa document theo ID
     */
    public function deleteDocument(string $id): bool
    {
        try {
            $data = [
                'delete' => ['id' => $id]
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->baseUrl}/solr/{$this->core}/update", $data);

            if ($response->successful()) {
                $this->commit();
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            Log::error('Solr deleteDocument failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🧹 Xóa documents theo query
     */
    public function deleteByQuery(string $query): bool
    {
        try {
            $data = [
                'delete' => ['query' => $query]
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->baseUrl}/solr/{$this->core}/update", $data);

            if ($response->successful()) {
                $this->commit();
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            Log::error('Solr deleteByQuery failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🏗️ Tạo core mới
     */
    public function createCore(string $coreName): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/solr/admin/cores", [
                    'action' => 'CREATE',
                    'name' => $coreName,
                    'instanceDir' => $coreName,
                    'configSet' => 'basic_configs'
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Solr createCore failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🧹 Xóa toàn bộ core
     */
    public function deleteCore(string $coreName): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/solr/admin/cores", [
                    'action' => 'UNLOAD',
                    'core' => $coreName,
                    'deleteInstanceDir' => 'true'
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Solr deleteCore failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔄 Commit changes
     */
    public function commit(): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/{$this->core}/update", [
                    'commit' => []
                ], [
                    'Content-Type' => 'application/json'
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Solr commit failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🏥 Health check
     */
    public function ping(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/solr/{$this->core}/admin/ping");

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Solr ping failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📊 Lấy thông tin core
     */
    public function getCoreInfo(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/solr/{$this->core}/admin/system");

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Solr getCoreInfo failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 🔍 Tìm kiếm với filters
     */
    public function searchWithFilters(string $query, array $filters = []): array
    {
        $params = [
            'q' => $query,
            'wt' => 'json',
            'rows' => 30,
            'start' => 0,
        ];

        // Add filters
        if (!empty($filters)) {
            $fq = [];
            foreach ($filters as $field => $value) {
                if (is_array($value)) {
                    $fq[] = $field . ':(' . implode(' OR ', $value) . ')';
                } else {
                    $fq[] = $field . ':' . $value;
                }
            }
            $params['fq'] = $fq;
        }

        return $this->search($query, $params);
    }

    /**
     * 📈 Lấy search statistics
     */
    public function getSearchStats(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/solr/{$this->core}/admin/mbeans", [
                    'stats' => 'true',
                    'cat' => 'QUERYHANDLER'
                ]);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Solr getSearchStats failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 📊 Đếm số documents trong Solr
     */
    public function getDocumentCount(): int
    {
        try {
            $response = $this->search('*:*', ['rows' => 0]);
            return $response['response']['numFound'] ?? 0;
        } catch (\Throwable $e) {
            Log::error('Solr getDocumentCount failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * 🗑️ Xóa tất cả documents trong Solr
     */
    public function clearIndex(): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/{$this->core}/update", [
                    'delete' => [
                        'query' => '*:*'
                    ]
                ], [
                    'Content-Type' => 'application/json'
                ]);

            if ($response->successful()) {
                $this->commit();
                return true;
            }
            return false;
        } catch (\Throwable $e) {
            Log::error('Solr clearIndex failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📝 Thêm document vào Solr
     */
    public function addDocument(array $document): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/{$this->core}/update", [
                    'add' => [
                        'doc' => $document
                    ]
                ], [
                    'Content-Type' => 'application/json'
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Solr addDocument failed: ' . $e->getMessage());
            return false;
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\ElasticSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ElasticSearchController extends Controller
{
    protected $search;

    public function __construct(ElasticSearchService $search)
    {
        $this->search = $search;
    }

    /**
     * 🔍 Tìm kiếm chính (Enter trong thanh tìm kiếm)
     */
    public function index(Request $request)
    {
        $keyword = trim($request->get('q', ''));

        if (empty($keyword)) {
            return response()->json([
                'count' => 0,
                'data' => [],
                'message' => 'No keyword provided',
            ]);
        }

        $query = [
            'query' => [
                'bool' => [
                    'should' => [

                        // 1. PREFIX MATCH (lap → laptop)
                        [
                            'match_phrase_prefix' => [
                                'title' => [
                                    'query' => $keyword,
                                    'boost' => 6,
                                ]
                            ]
                        ],

                     
                        [
                            'match' => [
                                'title' => [
                                    'query' => $keyword,
                                    'boost' => 6,
                                    'fuzziness' => 2,
                                    'prefix_length' => 1
                                ]
                            ]
                        ],

                        // 3. DESCRIPTION PREFIX (ưu tiên ít hơn title)
                        [
                            'wildcard' => [
                                'description' => [
                                    'value' => "{$keyword}*",
                                    'boost' => 2
                                ]
                            ]
                        ],

                        // 4. LIGHT FUZZY description
                        [
                            'match' => [
                                'description' => [
                                    'query' => $keyword,
                                    'fuzziness' => 1,
                                    'boost' => 1
                                ]
                            ]
                        ],
                    ],

                    // BẮT BUỘC phải có ít nhất 1 match thực sự
                    'minimum_should_match' => 1
                ]
            ],

            // ❗ Quan trọng: loại sách bằng score
            'min_score' => 1,

            'size' => 30,

            '_source' => ['title', 'description', 'price', 'category_id', 'image'],

            'highlight' => [
                'fields' => [
                    'title' => new \stdClass(),
                    'description' => new \stdClass(),
                ],
                'pre_tags' => ['<mark>'],
                'post_tags' => ['</mark>'],
            ]
        ];


        $result = $this->search->customSearch('listings', $query);
        $hits   = $result['hits']['hits'] ?? [];
        $count  = count($hits);

        /**
         * 🧠 Ghi lại lịch sử tìm kiếm theo user
         */
        try {
            $userId = auth()->id() ?? 0;
            $this->search->logSearch($keyword, $count, $userId);
        } catch (\Throwable $e) {
            Log::error('❌ Save search history failed: ' . $e->getMessage());
        }

        return response()->json([
            'count' => $count,
            'data'  => $hits,
        ]);
    }

    /**
     * 💡 Gợi ý realtime (autocomplete như Google)
     */
    public function suggestions(Request $request)
    {
        $keyword = trim($request->get('q', ''));
        if (empty($keyword)) {
            return response()->json(['suggestions' => []]);
        }

        /**
         * 🎯 Smart suggestion: kết hợp 3 chiến lược
         * - bool_prefix → autocomplete theo đầu từ
         * - fuzziness → gõ sai chính tả vẫn ra
         * - phrase_prefix → cụm từ gần đúng
         */
        $query = [
            'query' => [
                'bool' => [
                    'should' => [
                        [
                            'multi_match' => [
                                'query' => $keyword,
                                'fields' => ['title^3'],
                                'type' => 'bool_prefix',
                            ],
                        ],
                        [
                            'multi_match' => [
                                'query' => $keyword,
                                'fields' => ['title^3'],
                                'fuzziness' => 'AUTO',
                                'prefix_length' => 1,
                                'minimum_should_match' => '60%',
                            ],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'title' => [
                                    'query' => $keyword,
                                    'max_expansions' => 20,
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
            '_source' => ['title'],
            'size' => 10,
        ];

        $result = $this->search->customSearch('listings', $query);

        $suggestions = collect($result['hits']['hits'] ?? [])
            ->pluck('_source.title')
            ->filter()
            ->unique()
            ->values()
            ->take(10);

        return response()->json(['suggestions' => $suggestions]);
    }

    /**
     * 📜 Trả về 10 từ khoá user đã tìm gần đây (theo timestamp desc)
     */
    public function history(Request $request)
    {
        $userId = auth()->id() ?? 0;

        $query = [
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['user_id' => $userId]],
                    ],
                ],
            ],
            'sort' => [
                ['timestamp' => ['order' => 'desc']],
            ],
            '_source' => ['keyword', 'timestamp', 'results_count'],
            'size' => 10,
        ];

        $res = $this->search->customSearch('search_history', $query);

        $history = collect($res['hits']['hits'] ?? [])
            ->pluck('_source')
            ->map(fn($h) => [
                'keyword'       => $h['keyword'] ?? '',
                'timestamp'     => $h['timestamp'] ?? null,
                'results_count' => $h['results_count'] ?? 0,
            ])
            ->values();

        return response()->json(['history' => $history]);
    }

    /**
     * 🧹 Xoá lịch sử tìm kiếm của user hiện tại
     */
    public function clearHistory()
    {
        try {
            $userId = auth()->id() ?? 0;
            $response = $this->search->deleteByQuery('search_history', [
                'bool' => [
                    'must' => [
                        ['term' => ['user_id' => $userId]],
                    ],
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'History cleared successfully!',
                'response' => $response,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ clearHistory error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error clearing history: ' . $e->getMessage(),
            ], 500);
        }
    }
}

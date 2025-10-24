<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SearchQualityAnalyzer
{
    protected $unifiedSearch;

    public function __construct(UnifiedSearchService $unifiedSearch)
    {
        $this->unifiedSearch = $unifiedSearch;
    }

    /**
     * 🧪 Analyze search quality for both engines
     */
    public function analyzeSearchQuality(array $testQueries = []): array
    {
        if (empty($testQueries)) {
            $testQueries = $this->getDefaultTestQueries();
        }

        $results = [];

        foreach (['elasticsearch', 'solr'] as $engine) {
            $results[$engine] = $this->analyzeEngineQuality($engine, $testQueries);
        }

        // Compare results
        $results['comparison'] = $this->compareSearchQuality($results['elasticsearch'], $results['solr']);

        return $results;
    }

    /**
     * 🔍 Analyze quality for a specific engine
     */
    private function analyzeEngineQuality(string $engine, array $testQueries): array
    {
        $engineResults = [];

        foreach ($testQueries as $query => $expectedResults) {
            $startTime = microtime(true);
            $searchResult = $this->unifiedSearch->searchBoth($query);
            $endTime = microtime(true);

            $responseTime = ($endTime - $startTime) * 1000;
            $actualResults = $searchResult[$engine]['results'] ?? [];
            $resultCount = count($actualResults);

            // Calculate quality metrics
            $qualityMetrics = $this->calculateQualityMetrics($query, $expectedResults, $actualResults);

            $engineResults[$query] = [
                'query' => $query,
                'response_time' => round($responseTime, 2),
                'result_count' => $resultCount,
                'quality_metrics' => $qualityMetrics,
                'top_results' => array_slice($actualResults, 0, 5) // Top 5 results
            ];
        }

        return $engineResults;
    }

    /**
     * 📊 Calculate quality metrics
     */
    private function calculateQualityMetrics(string $query, array $expectedResults, array $actualResults): array
    {
        // Basic metrics
        $precision = $this->calculatePrecision($expectedResults, $actualResults);
        $recall = $this->calculateRecall($expectedResults, $actualResults);
        $f1Score = $this->calculateF1Score($precision, $recall);

        // Vietnamese language specific metrics
        $vietnameseScore = $this->calculateVietnameseScore($query, $actualResults);
        
        // Relevance score (simplified)
        $relevanceScore = $this->calculateRelevanceScore($query, $actualResults);

        // Fuzzy matching score
        $fuzzyScore = $this->calculateFuzzyScore($query, $actualResults);

        return [
            'precision' => round($precision, 3),
            'recall' => round($recall, 3),
            'f1_score' => round($f1Score, 3),
            'vietnamese_score' => round($vietnameseScore, 3),
            'relevance_score' => round($relevanceScore, 3),
            'fuzzy_score' => round($fuzzyScore, 3),
            'overall_score' => round(($precision + $recall + $f1Score + $vietnameseScore + $relevanceScore + $fuzzyScore) / 6, 3)
        ];
    }

    /**
     * 🎯 Calculate precision
     */
    private function calculatePrecision(array $expectedResults, array $actualResults): float
    {
        if (empty($actualResults)) {
            return 0.0;
        }

        $relevantResults = 0;
        foreach ($actualResults as $result) {
            if ($this->isRelevantResult($result, $expectedResults)) {
                $relevantResults++;
            }
        }

        return $relevantResults / count($actualResults);
    }

    /**
     * 📈 Calculate recall
     */
    private function calculateRecall(array $expectedResults, array $actualResults): float
    {
        if (empty($expectedResults)) {
            return 1.0;
        }

        $foundRelevant = 0;
        foreach ($expectedResults as $expected) {
            if ($this->isResultFound($expected, $actualResults)) {
                $foundRelevant++;
            }
        }

        return $foundRelevant / count($expectedResults);
    }

    /**
     * ⚖️ Calculate F1 Score
     */
    private function calculateF1Score(float $precision, float $recall): float
    {
        if ($precision + $recall == 0) {
            return 0.0;
        }

        return 2 * ($precision * $recall) / ($precision + $recall);
    }

    /**
     * 🇻🇳 Calculate Vietnamese language handling score
     */
    private function calculateVietnameseScore(string $query, array $results): float
    {
        $score = 0.0;
        $totalResults = count($results);

        if ($totalResults == 0) {
            return 0.0;
        }

        foreach ($results as $result) {
            $title = $result['title'] ?? $result['_source']['title'] ?? '';
            $description = $result['description'] ?? $result['_source']['description'] ?? '';
            
            $text = strtolower($title . ' ' . $description);
            $queryLower = strtolower($query);

            // Check for exact Vietnamese matches
            if (strpos($text, $queryLower) !== false) {
                $score += 1.0;
            } else {
                // Check for Vietnamese without diacritics
                $textNoDiacritics = $this->removeVietnameseDiacritics($text);
                $queryNoDiacritics = $this->removeVietnameseDiacritics($queryLower);
                
                if (strpos($textNoDiacritics, $queryNoDiacritics) !== false) {
                    $score += 0.8;
                } else {
                    // Check for partial matches
                    $queryWords = explode(' ', $queryNoDiacritics);
                    $matchedWords = 0;
                    
                    foreach ($queryWords as $word) {
                        if (strpos($textNoDiacritics, $word) !== false) {
                            $matchedWords++;
                        }
                    }
                    
                    $score += ($matchedWords / count($queryWords)) * 0.6;
                }
            }
        }

        return $score / $totalResults;
    }

    /**
     * 🎯 Calculate relevance score
     */
    private function calculateRelevanceScore(string $query, array $results): float
    {
        $score = 0.0;
        $totalResults = count($results);

        if ($totalResults == 0) {
            return 0.0;
        }

        foreach ($results as $index => $result) {
            $title = $result['title'] ?? $result['_source']['title'] ?? '';
            $description = $result['description'] ?? $result['_source']['description'] ?? '';
            
            $text = strtolower($title . ' ' . $description);
            $queryLower = strtolower($query);

            // Position-based relevance (earlier results are more relevant)
            $positionScore = 1.0 - ($index / $totalResults) * 0.5;

            // Text match relevance
            $matchScore = 0.0;
            if (strpos($text, $queryLower) !== false) {
                $matchScore = 1.0;
            } else {
                $queryWords = explode(' ', $queryLower);
                $matchedWords = 0;
                
                foreach ($queryWords as $word) {
                    if (strpos($text, $word) !== false) {
                        $matchedWords++;
                    }
                }
                
                $matchScore = $matchedWords / count($queryWords);
            }

            $score += $positionScore * $matchScore;
        }

        return $score / $totalResults;
    }

    /**
     * 🔤 Calculate fuzzy matching score
     */
    private function calculateFuzzyScore(string $query, array $results): float
    {
        $score = 0.0;
        $totalResults = count($results);

        if ($totalResults == 0) {
            return 0.0;
        }

        foreach ($results as $result) {
            $title = $result['title'] ?? $result['_source']['title'] ?? '';
            $description = $result['description'] ?? $result['_source']['description'] ?? '';
            
            $text = strtolower($title . ' ' . $description);
            $queryLower = strtolower($query);

            // Calculate Levenshtein distance
            $distance = levenshtein($queryLower, $text);
            $maxLength = max(strlen($queryLower), strlen($text));
            
            if ($maxLength == 0) {
                $fuzzyScore = 1.0;
            } else {
                $fuzzyScore = 1.0 - ($distance / $maxLength);
            }

            $score += max(0, $fuzzyScore);
        }

        return $score / $totalResults;
    }

    /**
     * 🔍 Check if result is relevant
     */
    private function isRelevantResult(array $result, array $expectedResults): bool
    {
        $title = $result['title'] ?? $result['_source']['title'] ?? '';
        $description = $result['description'] ?? $result['_source']['description'] ?? '';
        
        $text = strtolower($title . ' ' . $description);

        foreach ($expectedResults as $expected) {
            if (strpos($text, strtolower($expected)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🔍 Check if expected result is found
     */
    private function isResultFound(string $expected, array $actualResults): bool
    {
        $expectedLower = strtolower($expected);

        foreach ($actualResults as $result) {
            $title = $result['title'] ?? $result['_source']['title'] ?? '';
            $description = $result['description'] ?? $result['_source']['description'] ?? '';
            
            $text = strtolower($title . ' ' . $description);
            
            if (strpos($text, $expectedLower) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🇻🇳 Remove Vietnamese diacritics
     */
    private function removeVietnameseDiacritics(string $text): string
    {
        $vietnamese = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ'
        ];

        $english = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd'
        ];

        return str_replace($vietnamese, $english, $text);
    }

    /**
     * 📊 Compare search quality between engines
     */
    private function compareSearchQuality(array $esResults, array $solrResults): array
    {
        $comparison = [
            'overall_winner' => null,
            'precision_winner' => null,
            'recall_winner' => null,
            'f1_score_winner' => null,
            'vietnamese_winner' => null,
            'relevance_winner' => null,
            'fuzzy_winner' => null,
            'response_time_winner' => null
        ];

        // Calculate averages
        $esAverages = $this->calculateAverages($esResults);
        $solrAverages = $this->calculateAverages($solrResults);

        // Compare metrics
        $comparison['precision_winner'] = $esAverages['precision'] > $solrAverages['precision'] ? 'elasticsearch' : 'solr';
        $comparison['recall_winner'] = $esAverages['recall'] > $solrAverages['recall'] ? 'elasticsearch' : 'solr';
        $comparison['f1_score_winner'] = $esAverages['f1_score'] > $solrAverages['f1_score'] ? 'elasticsearch' : 'solr';
        $comparison['vietnamese_winner'] = $esAverages['vietnamese_score'] > $solrAverages['vietnamese_score'] ? 'elasticsearch' : 'solr';
        $comparison['relevance_winner'] = $esAverages['relevance_score'] > $solrAverages['relevance_score'] ? 'elasticsearch' : 'solr';
        $comparison['fuzzy_winner'] = $esAverages['fuzzy_score'] > $solrAverages['fuzzy_score'] ? 'elasticsearch' : 'solr';
        $comparison['response_time_winner'] = $esAverages['response_time'] < $solrAverages['response_time'] ? 'elasticsearch' : 'solr';

        // Determine overall winner
        $esWins = 0;
        $solrWins = 0;

        foreach (['precision', 'recall', 'f1_score', 'vietnamese_score', 'relevance_score', 'fuzzy_score'] as $metric) {
            if ($esAverages[$metric] > $solrAverages[$metric]) {
                $esWins++;
            } elseif ($solrAverages[$metric] > $esAverages[$metric]) {
                $solrWins++;
            }
        }

        if ($esWins > $solrWins) {
            $comparison['overall_winner'] = 'elasticsearch';
        } elseif ($solrWins > $esWins) {
            $comparison['overall_winner'] = 'solr';
        } else {
            $comparison['overall_winner'] = 'tie';
        }

        return $comparison;
    }

    /**
     * 📊 Calculate averages from results
     */
    private function calculateAverages(array $results): array
    {
        $averages = [
            'precision' => 0,
            'recall' => 0,
            'f1_score' => 0,
            'vietnamese_score' => 0,
            'relevance_score' => 0,
            'fuzzy_score' => 0,
            'response_time' => 0
        ];

        if (empty($results)) {
            return $averages;
        }

        $count = count($results);
        foreach ($results as $result) {
            $metrics = $result['quality_metrics'] ?? [];
            $averages['precision'] += $metrics['precision'] ?? 0;
            $averages['recall'] += $metrics['recall'] ?? 0;
            $averages['f1_score'] += $metrics['f1_score'] ?? 0;
            $averages['vietnamese_score'] += $metrics['vietnamese_score'] ?? 0;
            $averages['relevance_score'] += $metrics['relevance_score'] ?? 0;
            $averages['fuzzy_score'] += $metrics['fuzzy_score'] ?? 0;
            $averages['response_time'] += $result['response_time'] ?? 0;
        }

        foreach ($averages as $key => $value) {
            $averages[$key] = $value / $count;
        }

        return $averages;
    }

    /**
     * 📝 Get default test queries
     */
    private function getDefaultTestQueries(): array
    {
        return [
            'sách giáo khoa' => ['sách', 'giáo khoa', 'textbook'],
            'laptop cũ' => ['laptop', 'máy tính', 'computer'],
            'máy tính bảng' => ['tablet', 'máy tính', 'bảng'],
            'bàn học sinh' => ['bàn', 'học', 'desk'],
            'xe đạp' => ['xe', 'đạp', 'bicycle'],
            'điện thoại' => ['phone', 'điện thoại', 'mobile']
        ];
    }

    /**
     * 🧪 Test specific Vietnamese language scenarios
     */
    public function testVietnameseScenarios(): array
    {
        $scenarios = [
            'diacritics' => [
                'sách giáo khoa' => 'Vietnamese with diacritics',
                'sach giao khoa' => 'Vietnamese without diacritics'
            ],
            'typos' => [
                'sach' => 'sách',
                'may tinh' => 'máy tính',
                'lapto' => 'laptop'
            ],
            'synonyms' => [
                'máy tính' => 'computer',
                'xe đạp' => 'bicycle',
                'điện thoại' => 'phone'
            ]
        ];

        $results = [];
        foreach ($scenarios as $scenarioName => $queries) {
            $results[$scenarioName] = $this->analyzeSearchQuality($queries);
        }

        return $results;
    }
}

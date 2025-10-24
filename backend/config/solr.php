<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Solr Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Apache Solr search engine integration
    |
    */

    'url' => env('SOLR_URL', 'http://solr:8983'),
    
    'core' => env('SOLR_CORE', 'listings'),
    
    'timeout' => env('SOLR_TIMEOUT', 30),
    
    'connection' => [
        'host' => env('SOLR_HOST', 'solr'),
        'port' => env('SOLR_PORT', 8983),
        'path' => env('SOLR_PATH', '/solr'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Solr Schema Configuration
    |--------------------------------------------------------------------------
    */
    
    'schema' => [
        'fields' => [
            'id' => [
                'type' => 'string',
                'indexed' => true,
                'stored' => true,
                'required' => true,
            ],
            'title' => [
                'type' => 'text_vi',
                'indexed' => true,
                'stored' => true,
                'multiValued' => false,
            ],
            'description' => [
                'type' => 'text_vi',
                'indexed' => true,
                'stored' => true,
                'multiValued' => false,
            ],
            'price' => [
                'type' => 'pfloat',
                'indexed' => true,
                'stored' => true,
            ],
            'category_id' => [
                'type' => 'pint',
                'indexed' => true,
                'stored' => true,
            ],
            'condition_grade' => [
                'type' => 'string',
                'indexed' => true,
                'stored' => true,
            ],
            'status' => [
                'type' => 'string',
                'indexed' => true,
                'stored' => true,
            ],
            'seller_id' => [
                'type' => 'pint',
                'indexed' => true,
                'stored' => true,
            ],
            'created_at' => [
                'type' => 'pdate',
                'indexed' => true,
                'stored' => true,
            ],
            'updated_at' => [
                'type' => 'pdate',
                'indexed' => true,
                'stored' => true,
            ],
        ],
        
        'copy_fields' => [
            [
                'source' => 'title',
                'dest' => 'text',
            ],
            [
                'source' => 'description',
                'dest' => 'text',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vietnamese Text Analysis
    |--------------------------------------------------------------------------
    */
    
    'analyzers' => [
        'text_vi' => [
            'type' => 'text',
            'analyzer' => 'vietnamese_analyzer',
        ],
    ],

    'field_types' => [
        'text_vi' => [
            'class' => 'solr.TextField',
            'analyzer' => [
                'tokenizer' => [
                    'class' => 'solr.StandardTokenizerFactory',
                ],
                'filters' => [
                    [
                        'class' => 'solr.LowerCaseFilterFactory',
                    ],
                    [
                        'class' => 'solr.ICUNormalize2FilterFactory',
                    ],
                    [
                        'class' => 'solr.StopFilterFactory',
                        'words' => 'lang/stopwords_vi.txt',
                        'ignoreCase' => true,
                    ],
                ],
            ],
        ],
    ],
];

<?php
// @codingStandardsIgnoreFile
declare(strict_types=1);

return [
    'storage' => [
        'admin' => [
            'adapter' => [
                'name' => 'memcached',
                'options' => [
                    'servers' => [
                        ['cache_server1', 11211],
                    ],
                    'ttl' => 3600,
                    'namespace' => 'admin',
                ],
            ]
        ],
        'front' => [
            'adapter' => [
                'name' => 'memcached',
                'options' => [
                    'servers' => [
                        ['cache_server1', 11211]
                    ],
                    'ttl' => 3600,
                    'namespace' => 'front',
                ],
            ]
        ],
        'route' => [
            'adapter' => [
                // 'name' => 'memcached',
                'name' => 'memory',
                'options' => [
                    // 'servers' => [
                    //     ['cache_server1', 11211]
                    // ],
                    'ttl' => 3600,
                    'namespace' => 'route',
                ],
            ]
        ],
    ],
    'default' => 'admin',
    'delegate' => [
        'default' => 'memcached',
        'adapter' => [
            'memory' => [
                'adapter' => [
                    'name' => 'memory',
                    'options' => [
                        'ttl' => 3600,
                        'namespace' => null,
                    ],
                ]
            ],
            'memcached' => [
                'adapter' => [
                    'name' => 'memcached',
                    'options' => [
                        'servers' => [
                            ['cache_server2', 11211]
                        ],
                        'ttl' => 3600,
                        'namespace' => null,
                    ],
                ]
            ]
        ]
    ],
];

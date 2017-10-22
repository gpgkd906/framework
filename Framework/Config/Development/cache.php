<?php
// @codingStandardsIgnoreFile
declare(strict_types=1);

return [
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

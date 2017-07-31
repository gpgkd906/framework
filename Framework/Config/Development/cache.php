<?php

return [
    'storage' => [
        'admin' => [
            'adapter' => [
                'name' => 'memcached',
                'options' => [
                    'servers' => [
                        ['cache_server1', 11211],
                        ['cache_server2', 11211]
                    ],
                    'ttl' => 3600,
                    'namespace' => 'admin',
                ],
            ]
        ],
    ],
    'default' => 'admin',
];

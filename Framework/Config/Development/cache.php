<?php

return [
    'storage' => [
        'admin' => [
            'adapter' => [
                'name' => 'memcached',
                'options' => [
                    'servers' => ['cache_server2'],
                    'ttl' => 3600,
                    'namespace' => 'admin',
                ],
            ]
        ],
    ],
    'default' => 'admin',
];

<?php

return [
    'default' => 'zh',
    'cache' => [
        'adapter' => [
            'name' => 'memcached',
            'options' => [
                'servers' => ['cache_server2'],
                'ttl' => 3600,
                'namespace' => 'translation',
            ],
        ]
    ],
];

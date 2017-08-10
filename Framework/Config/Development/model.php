<?php
declare(strict_types=1);

return [
    'connection' => [
        'type' => 'mysql',
        'driver' => 'pdo_mysql',
        'dsn' => [
            'host' => 'db',
            'dbname' => 'docker',
            'charset' => 'utf8',
        ],
        'user' => 'docker',
        'password' => 'docker',
    ],
    // 'cache' => [
    //     'type' => 'redis',
    //     'namespace' => 'docker',
    //     'connection' => [
    //         'host' => 'redis_server',
    //         'port' => 6379
    //     ]
    // ],
    'cache' => [
        'type' => 'memcached',
        'namespace' => 'docker',
        'connection' => [
            'host' => 'cache_server2',
            'port' => 11211
        ]
    ],
    'entityManager' => [
        'proxyDir' => null,
        'devMode' => false,
    ]
];

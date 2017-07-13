<?php

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
    'cache' => [
        'type' => 'redis',
        'connection' => [
            'host' => 'redis_server',
            'port' => 6379
        ]
    ],
    'entityManager' => [
        'proxyDir' => null,
        'devMode' => false,
    ]
];

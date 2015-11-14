<?php

return [
    'connection' => [
        'type' => 'mysql',
        'dsn' => [
            'host' => 'localhost',
            'dbname' => 'framework',
            'charset' => 'utf8',
        ],
        'user' => 'test',
        'password' => 'testtest',
    ],
    'Entity' => [
        'metaInfo' => [
            'type' => 'annotation',
            'cache' => null, //null, session
            'expiration' => 60 * 60 * 24,
        ],
    ]
];

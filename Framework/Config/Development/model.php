<?php

return [
    'connection' => [
        'type' => 'mysql',
        'dsn' => [
            'host' => 'db',
            'dbname' => 'docker',
            'charset' => 'utf8',
        ],
        'user' => 'docker',
        'password' => 'docker',
    ],
    'Entity' => [
        'metaInfo' => [
            'type' => 'annotation',
            'cache' => null, //null, session
            'expiration' => 60 * 60 * 24,
        ],
    ]
];

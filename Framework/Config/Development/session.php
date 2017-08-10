<?php
declare(strict_types=1);

return [
    // use zend-cache as authentication::storage, use zend-cache adapter config
    'storage' => [
        'adapter' => [
            'name' => 'memcached',
            'options' => [
                'servers' => [
                    ['session_server1', 11211],
                    ['session_server2', 11211]
                ],
            ],
        ]
    ],
    'options' => [

    ],
    'namespaces' => [
        'default'
    ]
];

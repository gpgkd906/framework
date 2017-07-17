<?php

return [
    // use zend-cache as authentication::storage, use zend-cache adapter config
    'storage' => [
        'adapter' => [
            'name' => 'memcached',
            'options' => [
                'servers' => ['cache_server1'],
            ],
        ]
    ],
    'session' => [

    ],
];

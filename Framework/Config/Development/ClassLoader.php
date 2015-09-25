<?php

return [
    'Event' => [
        'namespace' => 'Framework\Event',
        'isSingleton' => false,
        'classes' => [
            'EventManager' => [
                'Class' => 'Framework\Event\Event\EventManager',
                ]
        ]
    ],
    'Plugin' => [
        'namespace' => 'Framework\Plugin',
        'isSingleton' => true,
        'classes' => [
            'PluginManager' => [
                'Class' => 'Framework\Plugin\Plugin\PluginManager',
            ],
        ]
    ],
    'RouteModel' => [
        'namespace' => 'Framework\Route',
        'isSingleton' => true,
        'classes' => [
            'HttpRouteModel' => [
                'Class' => 'Framework\Route\HttpRouteModel',
            ],
        ]
    ],
    'ViewModel' => [
        'namespace' => 'Framework\ViewModel',
        'isSingleton' => false,
    ],
    'Controller' => [
        'namespace' => 'Framework\Controller',
        'isSingleton' => true,
    ],
    'Model' => [
        'namespace' => 'Framework\Model',
        'isSingleton' => true,
    ]
];

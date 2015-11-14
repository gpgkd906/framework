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
    'Service' => [
        'namespace' => 'Framework\Service',
        'isSingleton' => true,
        'classes' => [
            'SessionService' => [
                'Class' => 'Framework\Service\SessionService\SessionService',
            ],
            'EntityService' => [
                'Class' => 'Framework\Service\EntityService\EntityService',
            ],
            'CodeService' => [
                'Class' => 'Framework\Service\CodeService\CodeService',
            ],
        ],
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
        'classes' => [
        ]
    ],
    'Model' => [
        'namespace' => 'Framework\Model',
        'isSingleton' => true,
    ]
];

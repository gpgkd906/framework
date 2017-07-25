<?php

namespace Framework\Module\Cngo\AdminUser\View\ViewModel;

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\AdminUser\View\Layout\AdminLoginPageLayout;

class LoginViewModel extends FormViewModel
{
    protected $template = '/template/login.phtml';

    protected $useConfirm = false;

    protected $config = [
        'layout' => AdminLoginPageLayout::class,
        'script' => [
            '/place.js'
        ]
    ];

    protected $fieldset = [
        'adminLogin' => [
            'login' => [
                'type' => 'text',
                'inputSpecification' => [
                    'require' => true,
                    'validators' => [
                        [
                            'name' => 'NotEmpty',
                        ],
                    ]
                ],
                'attrs' => [
                    'class' => 'form-control',
                    'placeholder' => 'Login',
                ],
            ],
            'password' => [
                'type' => 'password',
                'inputSpecification' => [
                    'require' => true,
                    'validators' => [
                        [
                            'name' => 'NotEmpty',
                        ],
                    ]
                ],
                'attrs' => [
                    'class' => 'form-control',
                    'placeholder' => 'Password',
                ]
            ],
            'remeber' => [
                'type' => 'checkbox',
                'value' => ['Remeber Me' => 0],
                'attrs' => [
                ],
            ],
        ]
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}

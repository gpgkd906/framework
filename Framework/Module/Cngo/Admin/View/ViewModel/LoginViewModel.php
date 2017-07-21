<?php

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminLoginPageLayout;
use Form2\Validator;

class LoginViewModel extends FormViewModel
{
    protected $template = '/template/login.html';

    protected $config = [
        'layout' => AdminLoginPageLayout::class,
        'script' => [
            '/place.js'
        ]
    ];

    protected $fieldset = [
        'login' => [
            'login' => [
                'type' => 'text',
                'validator' => [
                    [Validator::Exists, "※必須入力"],
                ],
                'attrs' => [
                    'class' => 'form-control',
                    'placeholder' => 'Login',
                ],
            ],
            'password' => [
                'type' => 'password',
                'validator' => [
                    [Validator::Exists, "※必須入力"],
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

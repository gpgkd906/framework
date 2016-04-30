<?php

namespace Framework\ViewModel\Admin;

use Framework\ViewModel\ViewModel\FormViewModel;
use Framework\ViewModel\Layout\AdminPageLayout;
use Form2\Validator;

class LoginViewModel extends FormViewModel
{
    protected $template = '/template/admin/login.html';
    
    protected $config = [
        'layout' => AdminPageLayout::class,
        'script' => [
            '/place.js'
        ]
    ];

    protected $data = [
        'title' => 'ログイン',
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
}
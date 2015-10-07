<?php

namespace Framework\ViewModel\Admin;

use Framework\ViewModel\ViewModel\FormViewModel;
use Form2\Validator;

class LoginViewModel extends FormViewModel
{
    protected $template = '/template/admin/login.html';
    
    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    protected $data = [
        'title' => 'ログイン',
    ];

    protected $fieldset = [
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
    ];

    public $listeners = [
        'Render' => 'onRender',
        'Complete' => 'onComplete',
    ];

    public function onRender()
    {
    }

    public function onComplete($event, $data)
    {
        var_dump('complete');
    }
}
<?php

namespace Framework\ViewModel\Admin\Cms;

use Framework\ViewModel\ViewModel\FormViewModel;
use Framework\ViewModel\Admin\Cms\Component;
use Framework\ViewModel\Layout\AdminPageLayout;
use Form2\Validator;

class PageRegisterViewModel extends FormViewModel
{
    protected $template = '/template/admin/dashboard.html';
        
    /* protected $fieldset = [ */
    /*     'login' => [ */
    /*         'login' => [ */
    /*             'type' => 'text', */
    /*             'validator' => [ */
    /*                 [Validator::Exists, "※必須入力"], */
    /*             ], */
    /*             'attrs' => [  */
    /*                 'class' => 'form-control', */
    /*                 'placeholder' => 'Login', */
    /*             ], */
    /*         ], */
    /*         'password' => [ */
    /*             'type' => 'password', */
    /*             'validator' => [ */
    /*                 [Validator::Exists, "※必須入力"], */
    /*             ], */
    /*             'attrs' => [ */
    /*                 'class' => 'form-control', */
    /*                 'placeholder' => 'Password', */
    /*             ] */
    /*         ], */
    /*         'remeber' => [ */
    /*             'type' => 'checkbox', */
    /*             'value' => ['Remeber Me' => 0], */
    /*             'attrs' => [ */

    /*             ], */
    /*         ], */
    /*     ] */
    /* ]; */
    
    protected $config = [
        'layout' => AdminPageLayout::class,
        'container' => [
            'Head' => [
                [
                    'viewModel' => 'Admin\NavbarViewModel',
                ],
            ],
            'Main' => [
                [
                    'viewModel' => Page\RegisterFormViewModel::class,
                ]
            ]
        ],
    ];

    protected $data = [
        'title' => '新規ページ作成',
    ];
}
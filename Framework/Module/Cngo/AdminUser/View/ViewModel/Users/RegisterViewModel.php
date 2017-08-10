<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\View\ViewModel\Users;

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;
use Framework\FormManager\Validator;

class RegisterViewModel extends FormViewModel
{
    protected $template = '/template/users/register.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    protected $fieldset = [
        'adminUser' => [
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
                    'placeholder' => 'ログインID',
                ],
            ],
            'name' => [
                'type' => 'text',
                'inputSpecification' => [
                ],
                'attrs' => [
                    'class' => 'form-control',
                    'placeholder' => '管理者名',
                ],
            ],
            'email' => [
                'type' => 'text',
                'inputSpecification' => [
                ],
                'attrs' => [
                    'class' => 'form-control',
                    'placeholder' => 'メールアドレス',
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
                    'placeholder' => 'パスワード',
                ]
            ],
            'passwordConfirm' => [
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
                    'placeholder' => 'パスワード確認',
                ]
            ],
        ]
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }
}

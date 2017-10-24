<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Fieldset;

use Std\FormManager\Fieldset;

/**
 * AdminUser Fieldset
 */
class AdminUserFieldset extends Fieldset
{
    protected $name = 'adminUser';

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getDefaultFieldset()
    {
        return [
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
                            'name' => 'Identical',
                            'options' => [
                                'token' => 'password',
                            ],
                        ],
                    ]
                ],
                'attrs' => [
                    'class' => 'form-control',
                    'placeholder' => 'パスワード確認',
                ]
            ]
        ];
    }
}

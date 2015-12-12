<?php

namespace Framework\Fieldset\Admin;

use Framework\Fieldset\AbstractFieldset;
use Form2\Validator;

class PageFieldset extends AbstractFieldset
{
    protected $name = 'page';

    protected $fieldset = [
        'pid' => [
            'type' => 'hidden',
            'attrs' => [
                'class' => 'form-control'
            ],
        ],
        'url' => [
            'type' => 'text',
            'validator' => [
                [Validator::Exists, "※必須入力"],
            ],
            'attrs' => [
                'class' => 'form-control',
                'placeholder' => 'url',
            ],
        ],
        'layout' => [
            'type' => 'select',
            'value' => [
                '管理画面' => 'admin',
                'フロント' => 'front',
            ],
            'attrs' => [
                'class' => 'form-control',
            ]
        ],
        'model' => [
            'type' => 'select',
            'value' => [
                
            ],
            'validator' => [
                [Validator::Exists, "※必須入力"],
            ],
            'attrs' => [
                'class' => 'form-control',
            ]
        ],
        'view' => [
            'type' => 'select',
            'value' => [],
            'attrs' => [
                'class' => 'form-control'
            ],
        ],
        'pageStatus' => [
            'type' => 'inLineRadio',
            'value' => ['非公開' => '0', '公開' => 1],
            'attrs' => [
                
            ],
        ],
        'authorizeType' => [
            'type' => 'inLineRadio',
            'value' => [
                '管理者認証' => 'admin', '会員認証' => 'member', '認証なし' => ''
            ],
        ],
        'keyword' => [
            'type' => 'text',
            'attrs' => [
                'class' => 'form-control',
                'placeholder' => 'keyword',
            ]
        ],
        'description' => [
            'type' => 'textarea',
            'attrs' => [
                'class' => 'form-control',
                'rows' => 3,
                'placeholder' => 'description',
            ]
        ],
        'submit' => [
            'type' => 'submit',
            'attrs' => [
                'class' => 'btn btn-danger btn-lg',
            ],
            'value' => '登録',
        ],
        'submitBlock' => [
            'type' => 'submit',
            'attrs' => [
                'class' => 'btn btn-danger btn-block btn-lg',
            ],
            'value' => '登録',
        ]
    ];
}

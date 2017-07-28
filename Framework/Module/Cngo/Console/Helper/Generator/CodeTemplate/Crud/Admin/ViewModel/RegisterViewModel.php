<?php

namespace Framework\Module\{Module}\View\ViewModel{Namespace};

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class RegisterViewModel extends FormViewModel
{
    protected $template = '/template{namespace}/register.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    protected $fieldset = [
        '{entity}' => [
            'column' => [
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
                    'placeholder' => 'カラム',
                ],
            ],
        ]
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..{ns}';
    }
}

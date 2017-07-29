<?php

namespace Framework\Module\Cms\Admin\View\ViewModel\Blog;

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class RegisterViewModel extends FormViewModel
{
    protected $template = '/template/blog/register.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    protected $fieldset = [
        'blog' => [
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
        return __DIR__ . '/../..';
    }
}

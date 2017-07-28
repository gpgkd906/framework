<?php

namespace Framework\Module\{Module}\View\ViewModel{Namespace};

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class {ViewModel} extends FormViewModel
{
    protected $template = '/template{namespace}/{template}.phtml';

    protected $useConfirm = true;

    protected $config = [
        'layout' => AdminPageLayout::class,
        'script' => [
        ]
    ];

    protected $fieldset = [
        'form' => [
            'field' => [
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
                    'placeholder' => 'Field',
                ],
            ],
        ]
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..{ns}';
    }
}

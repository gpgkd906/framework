<?php

namespace Framework\Module\Cms\Admin\View\ViewModel\Page;

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;
use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

class RegisterViewModel extends FormViewModel
{
    protected $template = '/template/page/register.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    protected $fieldset = null;

    public function getFieldset()
    {
        if ($this->fieldset === null) {
            $this->fieldset = [
                'controller' => [
                    'class' => [
                        'type' => 'select',
                        'value' => $this->getControllerClasses(),
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
                            'placeholder' => '页面分组名',
                        ],
                    ],
                    'description' => [
                        'type' => 'textarea',
                        'inputSpecification' => [
                        ],
                        'attrs' => [
                            'class' => 'form-control',
                            'placeholder' => '页面分组说明',
                        ],
                    ],
                    'menuFlag' => [
                        'type' => 'inlineradio',
                        'value' => [
                            0 => '不表示',
                            1 => '表示'
                        ],
                        'inputSpecification' => [
                            'require' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                ],
                            ]
                        ],
                        'attrs' => [
                            'placeholder' => '页面分组说明',
                        ],
                    ],
                ]
            ];
        }
        return $this->fieldset;
    }

    public function getControllerClasses()
    {
        $Router = ObjectManager::getSingleton()->get(RouterInterface::class);
        $routerList = $Router->getRouterList();
        $ControllerClass = [];
        foreach ($routerList as $url => $controller) {
            $pageInfo = $controller::getPageInfo();
            $ControllerClass[$controller] = $pageInfo['description'];
        }
        return $ControllerClass;
    }

    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }
}

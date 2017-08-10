<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\View\ViewModel\Section;

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;
use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

class RegisterViewModel extends FormViewModel
{
    protected $template = '/template/section/register.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    protected $fieldset = null;

    public function getFieldset()
    {
        if ($this->fieldset === null) {
            $this->fieldset = [
                'controllerGroup' => [
                    'name' => [
                        'type' => 'select',
                        'value' => $this->getControllerGroups(),
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
                            'require' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                ],
                            ]
                        ],
                        'attrs' => [
                            'class' => 'form-control',
                            'placeholder' => '页面分组说明',
                        ],
                    ],
                ]
            ];
        }
        return $this->fieldset;
    }

    public function getControllerGroups()
    {
        $Router = ObjectManager::getSingleton()->get(RouterInterface::class);
        $routerList = $Router->getRouterList();
        $ControllerGroups = [];
        foreach ($routerList as $url => $controller) {
            $pageInfo = $controller::getPageInfo();
            if (isset($pageInfo['group'])) {
                $group = $pageInfo['group'];
                $ControllerGroups[$group] = $group;
            }
        }
        return $ControllerGroups;
    }

    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }
}

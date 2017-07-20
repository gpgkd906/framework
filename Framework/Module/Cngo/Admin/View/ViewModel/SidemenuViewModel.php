<?php

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ObjectManager\ObjectManager;
use Framework\Router\RouterInterface;
use NumberFormatter;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;

class SidemenuViewModel extends AbstractViewModel
{
    const TRIGGER_MENUINIT = 'menu_init';
    const TRIGGER_MENUCREATED = 'menu_created';

    protected $template = '/template/component/sidemenu.html';
    protected $data = null;
    public $listeners = [
        parent::TRIGGER_INIT => 'onInit',
    ];

    public function onInit()
    {
        $this->triggerEvent(self::TRIGGER_MENUINIT);
        $Router = ObjectManager::getSingleton()->get(RouterInterface::class);
        $routerList = $Router->getRouterList();
        foreach ($routerList as $url => $controller) {
            if (!is_subclass_of($controller, AbstractAdminController::class)) {
                continue;
            }
            $data[$controller] = [
                'title' => $controller::getDescription(),
                'link' => '/' . $url,
                'priority' => $controller::getPriority(),
            ];
        }
        usort($data, function ($item1, $item2) {
            return $item1['priority'] > $item2['priority'] ? 1 : -1;
        });
        $this->setData($data);
        $this->triggerEvent(self::TRIGGER_MENUCREATED);
    }

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}

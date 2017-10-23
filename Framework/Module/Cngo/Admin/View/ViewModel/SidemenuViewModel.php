<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;
use Framework\ObjectManager\ObjectManager;
use Framework\Router\RouterManagerInterface;
use NumberFormatter;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;

class SidemenuViewModel extends AbstractViewModel
{
    const TRIGGER_MENUINIT = 'menu_init';
    const TRIGGER_MENUCREATED = 'menu_created';

    protected $template = '/template/sidemenu.phtml';
    protected $data = null;
    public $listeners = [
        self::TRIGGER_INIT => 'onInit',
    ];

    public function onInit()
    {
        $this->triggerEvent(self::TRIGGER_MENUINIT);
        $Router = ObjectManager::getSingleton()->get(RouterManagerInterface::class)->getMatched();
        $routerList = $Router->getRouterList();
        foreach ($routerList as $url => $controller) {
            if (!is_subclass_of($controller, AbstractAdminController::class)) {
                continue;
            }
            $pageInfo = $controller::getPageInfo();
            if (!$pageInfo['menu']) {
                continue;
            }
            $controllerData = [
                'title' => $pageInfo['description'],
                'link' => '/' . $url,
                'priority' => $pageInfo['priority'],
                'icon' => $pageInfo['icon'] ?? substr($pageInfo['description'], 0, 1),
            ];
            if (isset($pageInfo['group'])) {
                $group = $pageInfo['group'];
                if (!isset($data[$group])) {
                    $data[$group] = [
                        'title' => $group,
                        'link' => 'javascript::void(0)',
                        'child' => [],
                        'priority' => 1,
                        'icon' => $pageInfo['groupIcon'] ?? substr($pageInfo['description'], 0, 1),
                    ];
                }
                $data[$group]['child'][$controller] = $controllerData;
            } else {
                $data[$controller] = $controllerData;
            }
        }
        usort($data, function ($item1, $item2) {
            return $item1['priority'] > $item2['priority'] ? 1 : -1;
        });
        $data = new \ArrayIterator($data);
        $this->setData($data);
        $this->triggerEvent(self::TRIGGER_MENUCREATED);
    }

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}

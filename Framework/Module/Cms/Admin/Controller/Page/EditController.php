<?php

namespace Framework\Module\Cms\Admin\Controller\Page;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Page\EditViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\Controller;

class EditController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    private $Controller;

    public function index($id)
    {
        $this->Controller = $this->getEntityManager()->getRepository(Controller::class)->find($id);
        if (!$this->Controller) {
            $this->getRouter()->redirect(ListController::class);
        }
        return ViewModelManager::getViewModel([
            'viewModel' => EditViewModel::class,
            'data' => [
                'controller' => $this->Controller,
            ],
            'listeners' => [
                EditViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onEditComplete']
            ],
        ]);
    }

    public function onEditComplete(\Framework\EventManager\Event $event)
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $controller = $ViewModel->getForm()->getData()['controller'];
            $Controller = $this->Controller;
            $Controller->fromArray($controller);
            $this->getEntityManager()->merge($Controller);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "页面信息編集",
            "priority" => 0,
            "menu" => false
        ];
    }
}

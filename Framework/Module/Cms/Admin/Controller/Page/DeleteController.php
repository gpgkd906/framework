<?php

namespace Framework\Module\Cms\Admin\Controller\Page;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Page\DeleteViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\Controller;

class DeleteController extends AbstractAdminController implements EntityManagerAwareInterface
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
            'viewModel' => DeleteViewModel::class,
            'data' => [
                'controller' => $this->Controller,
            ],
            'listeners' => [
                DeleteViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onDeleteComplete']
            ],
        ]);
    }

    public function onDeleteComplete(\Framework\EventManager\Event $event)
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $Controller = $this->Controller;
            $Controller->setDeleteFlag(true);
            $this->getEntityManager()->merge($Controller);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "页面信息削除",
            "priority" => 0,
            "menu" => false
        ];
    }
}

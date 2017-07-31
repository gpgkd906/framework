<?php

namespace Framework\Module\Cms\Admin\Controller\Section;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Section\EditViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\ControllerGroup;

class EditController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    private $ControllerGroup;

    public function index($id)
    {
        $this->ControllerGroup = $this->getEntityManager()->getRepository(ControllerGroup::class)->find($id);
        if (!$this->ControllerGroup) {
            $this->getRouter()->redirect(ListController::class);
        }
        return ViewModelManager::getViewModel([
            'viewModel' => EditViewModel::class,
            'data' => [
                'controllerGroup' => $this->ControllerGroup,
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
            $controllerGroup = $ViewModel->getForm()->getData()['controllerGroup'];
            $ControllerGroup = $this->ControllerGroup;
            $ControllerGroup->fromArray($controllerGroup);
            $this->getEntityManager()->merge($ControllerGroup);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "页面分组編集",
            "priority" => 0,
            "menu" => false
        ];
    }
}

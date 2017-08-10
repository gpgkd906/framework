<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\Controller\Section;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Section\DeleteViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\ControllerGroup;

class DeleteController extends AbstractAdminController implements EntityManagerAwareInterface
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
            'viewModel' => DeleteViewModel::class,
            'data' => [
                'controllerGroup' => $this->ControllerGroup,
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
            $ControllerGroup = $this->ControllerGroup;
            $ControllerGroup->setDeleteFlag(true);
            $this->getEntityManager()->merge($ControllerGroup);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "页面分组削除",
            "priority" => 0,
            "menu" => false
        ];
    }
}

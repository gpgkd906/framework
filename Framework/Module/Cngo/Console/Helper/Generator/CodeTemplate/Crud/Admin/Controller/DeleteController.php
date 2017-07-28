<?php

namespace Framework\Module\{Module}\Controller{Namespace};

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\{Module}\View\ViewModel{Namespace}\DeleteViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\{Module}\Entity\{Entity};

class DeleteController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    private ${Entity};

    public function index($id)
    {
        $this->{Entity} = $this->getEntityManager()->getRepository({Entity}::class)->find($id);
        if (!$this->{Entity}) {
            $this->getRouter()->redirect(ListController::class);
        }
        return ViewModelManager::getViewModel([
            'viewModel' => DeleteViewModel::class,
            'data' => [
                '{entity}' => $this->{Entity},
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
            ${Entity} = $this->{Entity};
            ${Entity}->setDeleteFlag(true);
            $this->getEntityManager()->merge(${Entity});
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "削除",
            "priority" => 0,
            "menu" => false
        ];
    }
}

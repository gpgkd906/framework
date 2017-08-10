<?php
declare(strict_types=1);

namespace Framework\Module\{Module}\Controller{Namespace};

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\{Module}\View\ViewModel{Namespace}\EditViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\{Module}\Entity\{Entity};

class EditController extends AbstractAdminController implements EntityManagerAwareInterface
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
            'viewModel' => EditViewModel::class,
            'data' => [
                '{entity}' => $this->{Entity},
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
            ${entity} = $ViewModel->getForm()->getData()['{entity}'];
            ${Entity} = $this->{Entity};
            ${Entity}->fromArray(${entity});
            $this->getEntityManager()->merge(${Entity});
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "編集",
            "priority" => 0,
            "menu" => false
        ];
    }
}

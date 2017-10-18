<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\AdminUser\View\ViewModel\Users\DeleteViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\AdminUser\Entity\Users;

class DeleteController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    private $AdminUser;

    public function index($id): DeleteViewModel
    {
        $this->AdminUser = $this->getEntityManager()->getRepository(Users::class)->find($id);
        if (!$this->AdminUser) {
            $this->getRouter()->redirect(ListController::class);
        }
        return $this->getViewModelManager()->getViewModel([
            'viewModel' => DeleteViewModel::class,
            'data' => [
                'adminUser' => $this->AdminUser,
            ],
            'listeners' => [
                DeleteViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onDeleteComplete']
            ],
        ]);
    }

    public function onDeleteComplete(\Framework\EventManager\Event $event): void
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $AdminUser = $this->AdminUser;
            $AdminUser->setDeleteFlag(true);
            $this->getEntityManager()->merge($AdminUser);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo(): array
    {
        return [
            "description" => "管理者削除",
            "priority" => 0,
            "menu" => false
        ];
    }
}

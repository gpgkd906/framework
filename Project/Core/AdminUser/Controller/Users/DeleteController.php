<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Controller\Users;

use Project\Core\Admin\Controller\AbstractAdminController;
use Std\ViewModel\ViewModelManager;
use Project\Core\AdminUser\View\ViewModel\Users\DeleteViewModel;
use Std\Repository\EntityManagerAwareInterface;
use Project\Core\AdminUser\Entity\Users;

class DeleteController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Std\Repository\EntityManagerAwareTrait;
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

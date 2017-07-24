<?php

namespace Framework\Module\Cngo\Admin\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\Users\EditViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\Admin\Entity\AdminUsers;

class EditController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    private $AdminUser;

    public function index($id)
    {
        $this->AdminUser = $this->getEntityManager()->getRepository(AdminUsers::class)->find($id);
        if (!$this->AdminUser) {
            $this->getRouter()->redirect(ListController::class);
        }
        return ViewModelManager::getViewModel([
            'viewModel' => EditViewModel::class,
            'data' => [
                'adminUser' => $this->AdminUser,
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
            $adminUser = $ViewModel->getForm()->getData()['adminUser'];
            if ($adminUser['password']) {
                $adminUser['password'] = $this->getAuthentication()->passwordHash($adminUser['password']);
            }
            $AdminUser = $this->AdminUser;
            $AdminUser->fromArray($adminUser);
            $this->getEntityManager()->merge($AdminUser);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "管理者編集",
            "priority" => 0,
            "menu" => false
        ];
    }
}

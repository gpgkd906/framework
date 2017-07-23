<?php

namespace Framework\Module\Cngo\Admin\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\Users\RegisterViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\Admin\Entity\AdminUsers;

class RegisterController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
          'viewModel' => RegisterViewModel::class,
          'listeners' => [
              RegisterViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onRegisterComplete']
          ]
        ]);
    }

    public function onRegisterComplete(\Framework\EventManager\Event $event)
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->validate()) {
            $adminUser = $ViewModel->getForm()->getData()['adminUser'];
            $adminUser['password'] = $this->getAuthentication()->passwordHash($adminUser['password']);
            $AdminUser = new AdminUsers();
            $AdminUser->fromArray($adminUser);
            $this->getEntityManager()->persist($AdminUser);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getDescription()
    {
        return "管理者登録";
    }

    public static function getPriority()
    {
        return 2;
    }
}

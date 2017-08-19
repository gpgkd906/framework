<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\AdminUser\View\ViewModel\Users\EditViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\AdminUser\Entity\AdminUsers;
use Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareInterface;

class EditController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    use \Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareTrait;
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
            } else {
                unset($adminUser['password']);
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

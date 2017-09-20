<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\AdminUser\View\ViewModel\Users\RegisterViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\AdminUser\Entity\Users;
use Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareInterface;

class RegisterController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    use \Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareTrait;

    public function index(): RegisterViewModel
    {
        return ViewModelManager::getViewModel([
          'viewModel' => RegisterViewModel::class,
          'listeners' => [
              RegisterViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onRegisterComplete']
          ]
        ]);
    }

    public function onRegisterComplete(\Framework\EventManager\Event $event): void
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $adminUser = $ViewModel->getForm()->getData()['adminUser'];
            $adminUser['password'] = $this->getAuthentication()->passwordHash($adminUser['password']);
            $AdminUser = new Users();
            $AdminUser->fromArray($adminUser);
            $this->getEntityManager()->persist($AdminUser);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo(): array
    {
        return [
            'description' => '管理者登録',
            'priority' => 2,
            'menu' => true,
            'icon' => '<i class="mdi mdi-account-edit fa-fw" data-icon="v"></i>',
            'group' => '管理者管理',
            'groupIcon' => '<i class="icon-people"></i>',
        ];
    }
}

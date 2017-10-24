<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Controller\Users;

use Project\Core\Admin\Controller\AbstractAdminController;
use Std\ViewModel\ViewModelManager;
use Project\Core\AdminUser\View\ViewModel\Users\RegisterViewModel;
use Std\Repository\EntityManagerAwareInterface;
use Project\Core\AdminUser\Entity\Users;
use Project\Core\AdminUser\Authentication\AuthenticationAwareInterface;

class RegisterController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Std\Repository\EntityManagerAwareTrait;
    use \Project\Core\AdminUser\Authentication\AuthenticationAwareTrait;

    public function index(): RegisterViewModel
    {
        return $this->getViewModelManager()->getViewModel([
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

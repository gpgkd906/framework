<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Controller;

use Std\Controller\AbstractController;
use Std\ViewModel\ViewModelManager;
use Project\Core\AdminUser\View\ViewModel\LoginViewModel;
use Project\Core\Admin\Controller\AbstractAdminController;
use Project\Core\AdminUser\Controller\LoginController;
use Project\Core\AdminUser\Authentication\AuthenticationAwareInterface;

class LogoutController extends AbstractAdminController implements
    AuthenticationAwareInterface
{
    use \Project\Core\AdminUser\Authentication\AuthenticationAwareTrait;

    public function index(): void
    {
        $this->getAuthentication()->clearIdentity();
        $this->getRouter()->redirect(LoginController::class);
    }

    public static function getPageInfo(): array
    {
        return [
            "description" => "ログアウト",
            "priority" => 99,
            "menu" => true,
            'icon' => '<i class="mdi mdi-logout fa-fw"></i>',
        ];
    }
}

<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Controller;

use Std\Controller\AbstractController;
use Std\ViewModel\ViewModelManager;
use Project\Core\AdminUser\View\ViewModel\LoginViewModel;
use Project\Core\AdminUser\Authentication\AuthenticationAwareInterface;
use Project\Core\Admin\Controller\DashboardController;

class LoginController extends AbstractController implements
    AuthenticationAwareInterface
{
    use \Project\Core\AdminUser\Authentication\AuthenticationAwareTrait;

    public function index(): LoginViewModel
    {
        if ($this->getAuthentication()->hasIdentity()) {
            $this->getRouter()->redirect(DashboardController::class);
        }
        return $this->getViewModelManager()->getViewModel([
            'viewModel' => LoginViewModel::class,
            'listeners' => [
                LoginViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onLoginComplete']
            ]
        ]);
    }

    public function onLoginComplete(\Framework\EventManager\Event $event): void
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $adminLogin = $ViewModel->getForm()->getData()['adminLogin'];
            $this->getAuthentication()->login($adminLogin['login'], $adminLogin['password']);
            if ($this->getAuthentication()->hasIdentity()) {
                $this->getRouter()->redirect(DashboardController::class);
            } else {
                $ViewModel->getForm()->login->forceError('test');
            }
        }
    }

    public static function getPageInfo(): array
    {
        return [
            "description" => "ログイン",
            "priority" => 0,
            "menu" => false
        ];
    }
}

<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Controller\Users;

use Project\Core\Admin\Controller\AbstractAdminController;
use Std\ViewModel\ViewModelManager;
use Project\Core\AdminUser\View\ViewModel\Users\EditViewModel;
use Std\Repository\EntityManagerAwareInterface;
use Project\Core\AdminUser\Entity\Users;
use Project\Core\AdminUser\Authentication\AuthenticationAwareInterface;

use Std\Router\Http\Router;

class EditController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Std\Repository\EntityManagerAwareTrait;
    use \Project\Core\AdminUser\Authentication\AuthenticationAwareTrait;
    private $AdminUser;

    public function index($id): EditViewModel
    {
        $this->AdminUser = $this->getEntityManager()->getRepository(Users::class)->find($id);
        if (!$this->AdminUser) {
            $this->getRouter()->redirect(ListController::class);
        }
        return $this->getViewModelManager()->getViewModel([
            'viewModel' => EditViewModel::class,
            'data' => [
                'adminUser' => $this->AdminUser,
            ],
            'listeners' => [
                EditViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onEditComplete']
            ],
        ]);
    }

    public function onEditComplete(\Framework\EventManager\Event $event): void
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

    public static function getPageInfo(): array
    {
        return [
            "description" => "管理者編集",
            "priority" => 0,
            "menu" => false
        ];
    }
}

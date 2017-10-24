<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Controller\Users;

use Project\Core\Admin\Controller\AbstractAdminController;
use Std\ViewModel\ViewModelManager;
use Project\Core\AdminUser\View\ViewModel\Users\ListViewModel;
use Std\Repository\EntityManagerAwareInterface;
use Project\Core\AdminUser\Entity\Users;

class ListController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Std\Repository\EntityManagerAwareTrait;

    public function index(): ListViewModel
    {
        return $this->getViewModelManager()->getViewModel([
            'viewModel' => ListViewModel::class,
            'data' => [
                'users' => $this->getEntityManager()->getRepository(Users::class)->findBy([
                    'deleteFlag' => 0
                ], ['usersId' => 'ASC'], 50)
            ]
        ]);
    }

    public static function getPageInfo(): array
    {
        return [
            'description' => '管理者一覧',
            'priority' => 1,
            'menu' => true,
            'icon' => '<i class="mdi mdi-account-multiple fa-fw" data-icon="v"></i>',
            'group' => '管理者管理',
            'groupIcon' => '<i class="mdi mdi-account"></i>',
        ];
    }
}

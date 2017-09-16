<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\AdminUser\View\ViewModel\Users\ListViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\AdminUser\Entity\AdminUsers;

class ListController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function index(): ListViewModel
    {
        return ViewModelManager::getViewModel([
            'viewModel' => ListViewModel::class,
            'data' => [
                'adminUsers' => $this->getEntityManager()->getRepository(AdminUsers::class)->findBy([
                    'deleteFlag' => 0
                ], ['adminUsersId' => 'ASC'], 50)
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

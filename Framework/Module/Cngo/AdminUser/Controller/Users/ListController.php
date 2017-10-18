<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\AdminUser\View\ViewModel\Users\ListViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\AdminUser\Entity\Users;

class ListController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

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

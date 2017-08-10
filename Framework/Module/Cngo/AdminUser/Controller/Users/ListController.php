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

    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => ListViewModel::class,
            'data' => [
                'adminUsers' => $this->getEntityManager()->getRepository(AdminUsers::class)->findBy([
                    'deleteFlag' => 0
                ], ['adminUsersId' => 'ASC'], 50),
                'test' => '<script>alert(123);</script>'
            ]
        ]);
    }

    public static function getPageInfo()
    {
        return [
            'description' => '管理者一覧',
            'priority' => 1,
            'menu' => true,
            'group' => '管理者管理',
        ];
    }
}

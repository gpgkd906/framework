<?php

namespace Framework\Module\Cngo\Admin\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\Users\ListViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\Admin\Entity\AdminUsers;

class ListController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => ListViewModel::class,
            'data' => [
                'entities' => $this->getEntityManager()->getRepository(AdminUsers::class)->findBy([
                    'deleteFlag' => 0
                ], ['adminUsersId' => 'ASC'], 50)
            ]
        ]);
    }

    public static function getDescription()
    {
        return "管理者一覧";
    }

    public static function getPriority()
    {
        return 1;
    }
}

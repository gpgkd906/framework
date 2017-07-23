<?php

namespace Framework\Module\Cngo\Admin\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\Users\DeleteViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\Admin\Entity\AdminUsers;

class DeleteController extends AbstractAdminController
{
    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => DeleteViewModel::class
        ]);
    }

    public static function getPageInfo()
    {
        return [
            "description" => "管理者削除",
            "priority" => 0,
            "menu" => false
        ];
    }
}

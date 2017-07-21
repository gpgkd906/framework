<?php

namespace Framework\Module\Cngo\Admin\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\Users\DeleteViewModel;
class DeleteController extends AbstractAdminController
{
    public function index()
    {
        return ViewModelManager::getViewModel(['viewModel' => DeleteViewModel::class]);
    }

    public static function getDescription()
    {
        return "管理者削除";
    }

    public static function getPriority()
    {
        return 4;
    }
}

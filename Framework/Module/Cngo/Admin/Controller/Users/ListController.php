<?php

namespace Framework\Module\Cngo\Admin\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\Users\ListViewModel;

class ListController extends AbstractAdminController
{
    public function index()
    {
        return ViewModelManager::getViewModel(['viewModel' => ListViewModel::class]);
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

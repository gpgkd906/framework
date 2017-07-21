<?php

namespace Framework\Module\Cngo\Admin\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\Users\RegisterViewModel;
class RegisterController extends AbstractAdminController
{
    public function index()
    {
        return ViewModelManager::getViewModel(['viewModel' => RegisterViewModel::class]);
    }

    public static function getDescription()
    {
        return "管理者登録";
    }

    public static function getPriority()
    {
        return 2;
    }
}

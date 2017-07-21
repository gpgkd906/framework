<?php

namespace Framework\Module\Cngo\Admin\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\Users\EditViewModel;
class EditController extends AbstractAdminController
{
    public function index()
    {
        return ViewModelManager::getViewModel(['viewModel' => EditViewModel::class]);
    }

    public static function getDescription()
    {
        return "管理者編集";
    }

    public static function getPriority()
    {
        return 3;
    }
}

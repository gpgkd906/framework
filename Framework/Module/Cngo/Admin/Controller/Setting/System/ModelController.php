<?php

namespace Framework\Module\Cngo\Admin\Controller\Setting\System;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\ViewModel\Admin\Setting\System\ModelViewModel;
use Framework\Model\Cms\PageModel;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;

class ModelController extends AbstractAdminController
{
    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => ModelViewModel::class
        ]);
    }
}

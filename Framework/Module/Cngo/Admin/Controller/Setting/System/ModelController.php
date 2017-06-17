<?php

namespace Framework\Module\Cngo\Admin\Controller\Setting\System;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\ViewModel\Admin\Setting\System\ModelViewModel;
use Framework\Model\Cms\PageModel;

class ModelController extends AbstractController
{
    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => ModelViewModel::class
        ]);
    }
}

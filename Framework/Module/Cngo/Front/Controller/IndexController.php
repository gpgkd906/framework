<?php

namespace Framework\Module\Cngo\Front\Controller;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Front\View\ViewModel\IndexViewModel;

class IndexController extends AbstractController
{
    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => IndexViewModel::class,
        ]);
    }
}

<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Front\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModelManager;
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

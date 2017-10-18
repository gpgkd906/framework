<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Front\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Front\View\ViewModel\NotFoundViewModel;

class NotFoundController extends AbstractController
{
    public function index()
    {
        return $this->getViewModelManager()->getViewModel([
            "viewModel" => NotFoundViewModel::class,
        ]);
    }
}

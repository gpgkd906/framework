<?php
declare(strict_types=1);

namespace Project\Core\Front\Controller;

use Std\Controller\AbstractController;
use Std\ViewModel\ViewModelManager;
use Project\Core\Front\View\ViewModel\NotFoundViewModel;

class NotFoundController extends AbstractController
{
    public function index()
    {
        return $this->getViewModelManager()->getViewModel([
            "viewModel" => NotFoundViewModel::class,
        ]);
    }
}

<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Controller\Setting\System;

use Std\Controller\AbstractController;
use Std\ViewModel\ViewModelManager;
use Std\ViewModel\Admin\Setting\System\ModelViewModel;
use Framework\Model\Cms\PageModel;
use Project\Core\Admin\Controller\AbstractAdminController;

class ModelController extends AbstractAdminController
{
    public function index()
    {
        return $this->getViewModelManager()->getViewModel([
            'viewModel' => ModelViewModel::class
        ]);
    }
}

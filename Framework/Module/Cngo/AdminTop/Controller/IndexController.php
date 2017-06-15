<?php
namespace Framework\Module\Cngo\AdminTop\Controller;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class IndexController extends AbstractController
{
    public function index()
    {
        // $this->getObjectManager()->get('Service', 'EntityService')->getRepository('Users')->find(1);
        $this->exChange(DashboardController::class);
    }
}

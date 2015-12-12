<?php
namespace Framework\Controller\Admin;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class IndexController extends AbstractController
{
    public function index()
    {
        $this->getServiceManager()->get('Service', 'EntityService')->getRepository('Users')->find(1);
        $this->exChange(DashboardController::class);
    }    
}

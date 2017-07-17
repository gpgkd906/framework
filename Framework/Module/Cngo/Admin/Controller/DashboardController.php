<?php
namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\Event\EventManager;
use Framework\Module\Cngo\Admin\View\ViewModel\DashboardViewModel;
use Framework\Module\Cngo\Admin\Entity\User;

use Framework\Authentication\AuthenticationAwareInterface;

class DashboardController extends AbstractController implements AuthenticationAwareInterface
{
    use \Framework\Authentication\AuthenticationAwareTrait;
    public function index()
    {
        // $result = $auth->login('gpgkd906@gmail.com', '123');
        // var_dump($auth->getIdentity());
        // var_Dump($_SESSION);
        // $EntityManager = $this->getObjectManager()->get('EntityManager');
        // $UserRepository = $EntityManager->getRepository(User::class);
        //
        return ViewModelManager::getViewModel([
            "viewModel" => DashboardViewModel::class,
        ]);
    }
}

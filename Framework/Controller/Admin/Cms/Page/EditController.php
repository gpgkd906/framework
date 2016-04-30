<?php

namespace Framework\Controller\Admin\Cms\Page;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Model\Cms\PageModel;
use Framework\ViewModel\Admin\Cms\PageEditViewModel;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class EditController extends AbstractController
{    
    public function index()
    {        
        $param = $this->getParam();
        $PageModel = $this->getServiceManager()->get('Model', 'Cms\PageModel');
        $PageModel->setIdentify($param['pid']);
        return ViewModelManager::getViewModel([
            'viewModel' => PageEditViewModel::class,
            'listeners' => [
                'Complete' => [$this, 'onEditComplete'],
            ],
            'model' => $PageModel,
        ]);
    }

    public function onEditComplete($event)
    {
        //var_dump($data, 'complete');
        /* $Session = $this->getServiceManager()->getSessionService(); */
        /* $Session->setSection('LoginView', $data); */
        /* $this->addEventListener(AbstractController::TRIGGER_AFTER_ACTION, function() { */
        /*     $this->exChange(DashboardController::class); */
        /* }); */
    }

    public function test()
    {
        $acl = new Acl();
        
        $acl->addRole(new Role('guest'))
            ->addRole(new Role('member'))
            ->addRole(new Role('admin'));
        
        $parents = array('guest', 'member', 'admin');
        $acl->addRole(new Role('someUser'), $parents);
        
        $acl->addResource(new Resource('someResource'));
        
        $acl->deny('guest', 'someResource');
        $acl->allow('member', 'someResource');

        echo $acl->isAllowed('someUser', 'someResource') ? 'allowed' : 'denied';
    }
}

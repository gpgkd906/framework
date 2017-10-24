<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Authentication;

use Std\Authentication\AbstractAuthentication;
use Zend\Authentication\Result;
use Zend\Authentication\Storage;
use Zend\Authentication\Adapter;
use Std\Router\RouterAwareInterface;
use Project\Core\AdminUser\Authentication\Adapter\Admin;
use Project\Core\AdminUser\Controller\LoginController;
use Project\Core\AdminUser\Controller\Users\EditController;
use Framework\EventManager\EventManagerInterface;
use Project\Core\Admin\Controller\AbstractAdminController;
use Project\Core\Admin\View\ViewModel\NavbarViewModel;
use Std\Router\RouterManagerAwareInterface;

class Authentication extends AbstractAuthentication implements
    RouterManagerAwareInterface
{
    use \Std\Router\RouterManagerAwareTrait;

    public function __construct(Storage\StorageInterface $Storage = null, Adapter\AdapterInterface $Adapter = null)
    {
        if ($Adapter === null) {
            $Adapter = new Admin();
        }
        parent::__construct($Storage, $Adapter);
    }

    public function login($username, $password)
    {
        $Adapter = $this->getAdapter();
        $Adapter->setUsername($username);
        $Adapter->setPassword($password);
        $result = $this->authenticate($Adapter);
        return $result;
    }

    public function initListener()
    {
        $this->getObjectManager()->get(EventManagerInterface::class)
            ->addEventListener(
                AbstractAdminController::class,
                AbstractAdminController::TRIGGER_BEFORE_ACTION,
                [$this, 'adminAuthentication']
            )
            ->addEventListener(
                NavbarViewModel::class,
                NavbarViewModel::TRIGGER_INIT,
                [$this, 'getAdminProfile']
            );
    }

    public function adminAuthentication($event)
    {
        if (!$this->hasIdentity()) {
            $this->getRouterManager()->getMatched()->redirect(LoginController::class);
        }
    }

    public function getAdminProfile($event)
    {
        $data = $this->getIdentity();
        $data['profileEditUrl'] = $this->getRouterManager()->getMatched()->linkto(EditController::class, $data['usersId']);
        $event->getTarget()->getModel()->fromArray($data);
    }
}

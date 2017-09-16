<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\Authentication;

use Framework\Authentication\AbstractAuthentication;
use Zend\Authentication\Result;
use Zend\Authentication\Storage;
use Zend\Authentication\Adapter;
use Framework\Router\RouterAwareInterface;
use Framework\Module\Cngo\AdminUser\Authentication\Adapter\Admin;
use Framework\Module\Cngo\AdminUser\Controller\LoginController;
use Framework\Module\Cngo\AdminUser\Controller\Users\EditController;
use Framework\EventManager\EventManagerInterface;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\Module\Cngo\Admin\View\ViewModel\NavbarViewModel;

class Authentication extends AbstractAuthentication
{
    use \Framework\Router\RouterAwareTrait;

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
            $this->getRouter()->redirect(LoginController::class);
        }
    }

    public function getAdminProfile($event)
    {
        $data = $this->getIdentity();
        $data['profileEditUrl'] = $this->getRouter()->linkto(EditController::class, $data['adminUsersId']);
        $event->getTarget()->getModel()->fromArray($data);
    }
}

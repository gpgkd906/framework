<?php

namespace Framework\Module\{Module}\Controller{Namespace};

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\{Module}\View\ViewModel{Namespace}\RegisterViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\{Module}\Entity\{Entity};

class RegisterController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
          'viewModel' => RegisterViewModel::class,
          'listeners' => [
              RegisterViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onRegisterComplete']
          ]
        ]);
    }

    public function onRegisterComplete(\Framework\EventManager\Event $event)
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            ${entity} = $ViewModel->getForm()->getData()['{entity}'];
            $AdminUser = new {Entity}();
            $AdminUser->fromArray(${entity});
            $this->getEntityManager()->persist($AdminUser);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            'description' => '登録',
            'priority' => 2,
            'menu' => true,
            'group' => '管理',
        ];
    }
}

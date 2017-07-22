<?php

namespace Framework\Module\Cngo\Admin\Controller\Users;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\Users\RegisterViewModel;

class RegisterController extends AbstractAdminController
{
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
        if ($ViewModel->getForm()->validate()) {
            $loginInfo = $ViewModel->getForm()->getData()['adminUser'];

        } else {
            var_Dump($ViewModel->getForm()->getMessage());
        }
    }

    public static function getDescription()
    {
        return "管理者登録";
    }

    public static function getPriority()
    {
        return 2;
    }
}

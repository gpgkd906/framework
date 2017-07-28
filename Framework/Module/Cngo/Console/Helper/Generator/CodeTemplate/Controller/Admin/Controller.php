<?php

namespace Framework\Module\{Module}\Controller{Namespace};

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\{Module}\View\ViewModel{Namespace}\{ViewModel};

class {Controller} extends AbstractAdminController
{

    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => {ViewModel}::class,
            'listeners' => [
                {ViewModel}::TRIGGER_FORMCOMPLETE => [$this, 'onLoginComplete']
            ]
        ]);
    }

    public function onLoginComplete(\Framework\EventManager\Event $event)
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $data = $ViewModel->getForm()->getData();

        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "コントローラ",
            "priority" => 0,
            "menu" => false
        ];
    }
}

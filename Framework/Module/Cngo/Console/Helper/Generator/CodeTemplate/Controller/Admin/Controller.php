<?php
/**
 * PHP version 7
 * File {Controller}.php
 *
 * @category Controller
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Module\{Module}\Controller{Namespace};

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\{Module}\View\ViewModel{Namespace}\{ViewModel};

/**
 * Class {Controller}
 *
 * @category Controller
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class {Controller} extends AbstractAdminController
{

    /**
     * Method index
     *
     * @return {ViewModel}
     */
    public function index(): {ViewModel}
    {
        return ViewModelManager::getViewModel([
            'viewModel' => {ViewModel}::class,
            'listeners' => [
                {ViewModel}::TRIGGER_FORMCOMPLETE => [$this, 'onLoginComplete']
            ]
        ]);
    }

    /**
     * Method onLoginComplete
     *
     * @param  \Framework\EventManager\Event $event Event
     * @return void
     */
    public function onLoginComplete(\Framework\EventManager\Event $event): void
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $data = $ViewModel->getForm()->getData();
        }
    }

    /**
     * Method getPageInfo
     *
     * @return Array
     */
    public static function getPageInfo(): array
    {
        return [
            "description" => "コントローラ",
            "priority" => 0,
            "menu" => false,
            "icon" => '<i class="mdi mdi-av-timer fa-fw" data-icon="v"></i>'
        ];
    }
}

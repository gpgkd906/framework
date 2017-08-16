<?php
/**
 * PHP version 7
 * File HttpApplication.php
 * 
 * @category Module
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Application;

use Framework\ViewModel\ViewModelManager;
use Framework\EventManager\EventTargetInterface;
use Framework\Controller\ControllerInterface;
use Framework\Router\RouterInterface;
use Framework\Router\Http\Router;
use Exception;

/**
 * Class HttpApplication
 * 
 * @category Application
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class HttpApplication extends AbstractApplication implements EventTargetInterface
{

    use \Framework\EventManager\EventTargetTrait;
    const TRIGGER_ROUTEMISS = 'routemiss';

    private $_controller = null;

    /**
     * Method run
     *
     * @return void
     */
    public function run()
    {
        $config = $this->getConfig();
        $routeModel = $this->getObjectManager()->get(RouterInterface::class, Router::class);
        if ($routeModel->isFaviconRequest()) {
            $this->sendDummyFavicon();
        }

        ViewModelManager::setBasePath($config->get('ApplicationHost'));
        ViewModelManager::setObjectManager($this->getObjectManager());
        $request = $routeModel->dispatch();
        $Controller = null;
        if ($request['controller']) {
            $Controller = $this->getObjectManager()->get(ControllerInterface::class, $request['controller']);
            $action = $request['action'];
            if (!$Controller) {
                $this->triggerEvent(self::TRIGGER_ROUTEMISS, $request);
                $Controller = $this->getController();
            }
        }
        if (!$Controller instanceof ControllerInterface) {
            return $this->sendNotFound();
        }
        $Controller->callActionFlow($request['action'], $request['param']);
    }

    /**
     * Method setController
     *
     * @param Object $controller Controller
     * 
     * @return this
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * Method getController
     *
     * @return Object|null
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Method sendDummyFavicon
     *
     * @return void
     */
    public function sendDummyFavicon()
    {
        header('Content-Type: image/vnd.microsoft.icon');
        header('Content-length: 0');
        die();
    }

    /**
     * Method sendNotFound
     *
     * @return void
     */
    public function sendNotFound()
    {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        echo '404 Not Found';
    }
}

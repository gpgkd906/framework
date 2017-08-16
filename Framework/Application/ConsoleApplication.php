<?php
/**
 * PHP version 7
 * File ConsoleApplication.php
 * 
 * @category Module
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Application;

use Framework\Router\RouterInterface;
use Framework\Router\Console\Router;
use Framework\Controller\ControllerInterface;
use Exception;

/**
 * Class ConsoleApplication
 * 
 * @category Application
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ConsoleApplication extends AbstractApplication
{
    const DEFAULT_CONTROLLER_NAMESPACE = "Framework\Console";

    /**
     * Method run
     *
     * @return void
     */
    public function run()
    {
        $config = $this->getConfig();
        //route
        $routeModel = $this->getObjectManager()->get(RouterInterface::class, Router::class);

        $request = $routeModel->dispatch();
        $Controller = $this->getObjectManager()->get(ControllerInterface::class, $request['controller']);
        if ($Controller) {
            $action = $request['action'];
            $Controller->callActionFlow($request['action'], $request['param']);
        } else {
            throw new Exception("invalid console application");
        }
    }

    /**
     * Method setController
     *
     * @param Object $Controller Controller
     * 
     * @return void
     */
    public function setController($Controller)
    {
        $this->controller = $Controller;
        return $this;
    }

    /**
     * Method getController
     *
     * @return Object|null
     */
    public function getController()
    {
        return $this->controller;
    }
}

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
use Framework\Controller\ConsoleInterface;
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
    /**
     * Method run
     *
     * @return void
     */
    public function run()
    {
        $routeModel = $this->getObjectManager()->get(RouterManagerInterface::class)->getMatchedOrDefault();

        $request = $routeModel->dispatch();
        $Controller = $this->getObjectManager()->get(ConsoleInterface::class, $request['controller']);
        if (!$Controller instanceof ConsoleInterface) {
            throw new Exception("invalid console application");
        }
        $Controller->callActionFlow($request['action'], $request['param']);
    }
}

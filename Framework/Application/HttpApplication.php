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

use Framework\EventManager\EventTargetInterface;
use Std\Controller\ControllerInterface;
use Std\ViewModel\ViewModelManagerInterface;
use Std\Router\RouterManagerInterface;
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
class HttpApplication extends AbstractApplication
{
    /**
     * Method run
     *
     * @return void
     */
    public function run()
    {
        $routeModel = $this->getObjectManager()->get(RouterManagerInterface::class)->getMatchedOrDefault();
        if ($routeModel->isFaviconRequest()) {
            $this->sendDummyFavicon();
        }

        $request = $routeModel->dispatch();
        $Controller = $this->getObjectManager()->get(ControllerInterface::class, $request['controller']);
        if (!$Controller instanceof ControllerInterface) {
            return $this->sendNotFound();
        }
        $ViewModel = $Controller->callActionFlow($request['action'], $request['param']);
        $this->getObjectManager()->get(ViewModelManagerInterface::class)->render($ViewModel);
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

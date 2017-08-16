<?php
/**
 * PHP version 7
 * File AbstractController.php
 * 
 * @category Controller
 * @package  Framework\Controller
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Controller;

use Framework\Application\HttpApplication;
use Framework\ObjectManager\SingletonInterface;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ViewModel\ViewModelInterface;
use Framework\ViewModel\AbstractViewModel;
use Framework\EventManager\EventTargetInterface;
use Framework\Router\RouterAwareInterface;
use Framework\Service\SessionService\SessionServiceAwareInterface;
use Exception;

/**
 * Abstract Class AbstractController
 * 
 * @category Class
 * @package  Framework\Controller
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
abstract class AbstractController implements
    ControllerInterface,
    EventTargetInterface,
    SingletonInterface,
    ObjectManagerAwareInterface,
    RouterAwareInterface
{
    use \Framework\EventManager\EventTargetTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\Router\RouterAwareTrait;

    private static $_instance = [];

    //error
    const ERROR_ACTION_RETURN_IS_NOT_VIEWMODEL = "error: return-value is not valid view model from action %s ";

    //EVENT
    const TRIGGER_BEFORE_ACTION = 'beforeAction';
    const TRIGGER_AFTER_ACTION = 'afterAction';
    const TRIGGER_BEFORE_RESPONSE = 'beforeResponse';
    const TRIGGER_AFTER_RESPONSE = 'afterResponse';

    private $_controllerName = null;
    private $_ViewModel = null;

    /**
     * Method getSingleton
     *
     * @return ControllerInterface
     */
    public static function getSingleton()
    {
        $controllerName = static::class;
        if (!isset(self::$_instance[$controllerName])) {
            self::$_instance[$controllerName] = new $controllerName();
            self::$_instance[$controllerName]->setName($controllerName);
        }
        return self::$_instance[$controllerName];
    }

    /**
     * Protected Method __construct
     */
    protected function __construct()
    {
    }

    /**
     * Method callActionFlow
     *
     * @param string $action action
     * @param string $param  parameter
     * 
     * @return void
     */
    public function callActionFlow($action, $param)
    {
        $routeModel = $this->getRouter();
        $actionMethod = $routeModel->getAction();
        $this->triggerEvent(self::TRIGGER_BEFORE_ACTION);
        if (is_callable([$this, $actionMethod])) {
            if ($viewModel = $this->callAction($actionMethod, $param)) {
                $this->setViewModel($viewModel);
            }
        }
        $this->triggerEvent(self::TRIGGER_AFTER_ACTION);
        $viewModel = $this->getViewModel();
        if (isset($viewModel)) {
            if ($viewModel instanceof ViewModelInterface) {
                $this->triggerEvent(self::TRIGGER_BEFORE_RESPONSE);
                $this->callAction("response");
                $this->triggerEvent(self::TRIGGER_AFTER_RESPONSE);
            } else {
                $message = sprintf(self::ERROR_ACTION_RETURN_IS_NOT_VIEWMODEL, $this->getName() . "::" . $actionMethod);
                throw new Exception($message);
            }
        }
    }

    /**
     * Method callAction
     *
     * @param string $action action
     * @param string $param  parameter
     * 
     * @return ViewModel|null
     */
    protected function callAction($action, $param = [])
    {
        if (is_callable([$this, $action])) {
            if ($param === null) {
                $param = [];
            }
            return call_user_func_array([$this, $action], $param);
        }
    }

    /**
     * Method response
     *
     * @return void
     */
    public function response()
    {
        echo $this->getViewModel()->render();
    }

    /**
     * Method setName
     *
     * @param string $controllerName controllerName
     * 
     * @return this
     */
    public function setName($controllerName)
    {
        $this->_controllerName = $controllerName;
        return $this;
    }

    /**
     * Method getName
     *
     * @return string $controllerName controllerName
     */
    public function getName()
    {
        return $this->_controllerName;
    }

    /**
     * Method setViewModel
     *
     * @param ViewModel $ViewModel ViewModel
     * 
     * @return this
     */
    public function setViewModel($ViewModel)
    {
        $data = $ViewModel->getData();
        if (!isset($data['title'])) {
            $data['title'] = $this->getDescription();
            $ViewModel->setData($data);
        }
        $this->_ViewModel = $ViewModel;
        return $this;
    }

    /**
     * Method getViewModel
     *
     * @return ViewModel $_ViewModel ViewModel
     */
    public function getViewModel()
    {
        return $this->_ViewModel;
    }

    /**
     * Method getParam
     *
     * @return array $param parameter
     */
    public function getParam()
    {
        $param = $this->getRouter()->getParam();
        unset($param['req']);
        return $param;
    }

    /**
     * Method getPageInfo
     *
     * @return array $pageInfo PageInfo
     */
    public static function getPageInfo()
    {
        return [
            "description" => "PageInfo",
            "priority" => 0,
            "menu" => true
        ];
    }

    /**
     * Method getDescription
     *
     * @return string $descript PageDescription
     */
    public static function getDescription()
    {
        return static::getPageInfo()['description'];
    }
}

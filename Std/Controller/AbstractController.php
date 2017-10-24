<?php
/**
 * PHP version 7
 * File AbstractController.php
 *
 * @category Controller
 * @package  Std\Controller
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\Controller;

use Framework\Application\HttpApplication;
use Framework\ObjectManager\SingletonInterface;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Std\ViewModel\ViewModelManagerAwareInterface;
use Std\ViewModel\AbstractViewModel;
use Framework\EventManager\EventTargetInterface;
use Std\Router\RouterManagerAwareInterface;
use Std\SessionManager\SessionManagerAwareInterface;
use Exception;

/**
 * Abstract Class AbstractController
 *
 * @category Class
 * @package  Std\Controller
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
abstract class AbstractController implements
    ControllerInterface,
    EventTargetInterface,
    SingletonInterface,
    ObjectManagerAwareInterface,
    ViewModelManagerAwareInterface,
    RouterManagerAwareInterface
{
    use \Framework\EventManager\EventTargetTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\ObjectManager\SingletonTrait;
    use \Std\ViewModel\ViewModelManagerAwareTrait;
    use \Std\Router\RouterManagerAwareTrait;

    //error
    const ERROR_ACTION_RETURN_IS_NOT_VIEWMODEL = "error: return-value is not valid view model from action %s ";

    //EVENT
    const TRIGGER_BEFORE_ACTION = 'beforeAction';
    const TRIGGER_AFTER_ACTION = 'afterAction';

    private $_controllerName = null;
    private $_ViewModel = null;

    /**
     * Protected Method __construct
     */
    private function __construct()
    {
        $this->_controllerName = static::class;
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
        $routeModel = $this->getRouterManager()->getMatched();
        $actionMethod = $routeModel->getAction();
        $this->triggerEvent(self::TRIGGER_BEFORE_ACTION);
        if (is_callable([$this, $actionMethod])) {
            if ($viewModel = $this->callAction($actionMethod, $param)) {
                $this->setViewModel($viewModel);
            }
        }
        $this->triggerEvent(self::TRIGGER_AFTER_ACTION);
        return $this->getViewModel();
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
        $param = $this->getRouterManager()->getMatched()->getParam();
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
    public function getDescription()
    {
        return static::getPageInfo()['description'];
    }
}

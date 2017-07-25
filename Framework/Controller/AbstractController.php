<?php

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

    private static $instance = [];

    //error
    const ERROR_ACTION_RETURN_IS_NOT_VIEWMODEL = "error: return-value is not valid view model from action %s ";

    //EVENT
    const TRIGGER_BEFORE_ACTION = 'beforeAction';
    const TRIGGER_AFTER_ACTION = 'afterAction';
    const TRIGGER_BEFORE_RESPONSE = 'beforeResponse';
    const TRIGGER_AFTER_RESPONSE = 'afterResponse';

    private $controllerName = null;
    private $ViewModel = null;

    public static function getSingleton()
    {
        $controllerName = static::class;
        if (!isset(self::$instance[$controllerName])) {
            self::$instance[$controllerName] = new $controllerName();
            self::$instance[$controllerName]->setName($controllerName);
        }
        return self::$instance[$controllerName];
    }

    protected function __construct()
    {
    }

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

    protected function callAction($action, $param = [])
    {
        if (is_callable([$this, $action])) {
            if ($param === null) {
                $param = [];
            }
            return call_user_func_array([$this, $action], $param);
        }
    }

    public function response()
    {
        echo $this->getViewModel()->render();
    }

    public function setName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    public function getName()
    {
        return $this->controllerName;
    }

    public function setViewModel($ViewModel)
    {
        $data = $ViewModel->getData();
        if (!isset($data['title'])) {
            $data['title'] = $this->getDescription();
            $ViewModel->setData($data);
        }
        $this->ViewModel = $ViewModel;
    }

    public function getViewModel()
    {
        return $this->ViewModel;
    }

    public function getParam()
    {
        $param = $this->getRouter()->getParam();
        unset($param['req']);
        return $param;
    }

    public static function getPageInfo()
    {
        return [
            "description" => "PageInfo",
            "priority" => 0,
            "menu" => true
        ];
    }

    public static function getDescription()
    {
        return static::getPageInfo()['description'];
    }
}

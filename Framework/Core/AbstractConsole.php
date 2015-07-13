<?php

namespace Framework\Core;

use Framework\Core\Interfaces\ControllerInterface;
use Framework\Core\Interfaces\ViewModelInterface;
use Framework\Core\ViewModel\AbstractViewModel;
use Framework\Core\Interfaces\EventInterface;
use Exception;

abstract class AbstractConsole Extends AbstractController implements ControllerInterface, EventInterface
{    
    public function callActionFlow($action, $param)
    {
        if(is_callable([$this, $action])) {
            $this->callAction("beforeAction");
            $this->callAction($action, $param);
            $this->callAction("afterAction");
        } else {
            throw new Exception(sprintf("not found implementions for action[%s]", $action));
        }
    }
}

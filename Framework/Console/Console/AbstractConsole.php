<?php

namespace Framework\Console\Console;

use Framework\Controller\Controller\AbstractController;
use Exception;

abstract class AbstractConsole Extends AbstractController
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

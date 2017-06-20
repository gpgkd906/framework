<?php

namespace Framework\Controller\Controller;

use Exception;

abstract class AbstractConsole extends AbstractController
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

    abstract public function getDescription();

    abstract public function getHelp();

    public function getPriority()
    {
        return 99;
    }
}

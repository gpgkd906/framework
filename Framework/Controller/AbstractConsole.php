<?php

namespace Framework\Controller;

use Exception;

abstract class AbstractConsole extends AbstractController
{
    public function callActionFlow($action, $param)
    {
        if (is_callable([$this, $action])) {
            $this->triggerEvent(self::TRIGGER_BEFORE_RESPONSE);
            $this->callAction($action, $param);
            $this->triggerEvent(self::TRIGGER_AFTER_RESPONSE);
        } else {
            throw new Exception(sprintf("not found implementions for action[%s]", $action));
        }
    }

    public static function getDescription()
    {
        return 'input Class Description';
    }

    abstract public function getHelp();

    public function getPriority()
    {
        return 99;
    }
}

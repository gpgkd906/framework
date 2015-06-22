<?php

namespace Framework\Config;

use Framework\Core\Interfaces\ConfigModelInterface;
use Framework\Core\Interfaces\EventInterface;
use Framework\Core\AbstractConfigModel;
use Exception;

class ConfigModel extends AbstractConfigModel implements ConfigModelInterface 
{
    static protected function getFile($namespace, $configName)
    {
        if(static::$dir === null) {
            static::$dir = ROOT_DIR . "Framework/Config/" . $namespace . "/";
        }
        return static::$dir . $configName . ".php";
    }
}

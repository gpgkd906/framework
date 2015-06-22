<?php

namespace Framework\Config;

use Framework\Core\Interfaces\ConfigModelInterface;
use Framework\Core\Interfaces\EventInterface;
use Exception;

class ConfigModel implements ConfigModelInterface 
{
    static private function getFile($namespace, $configName)
    {
        if(static::$dir === null) {
            static::$dir = ROOT_DIR . "Framework/Config/" . $namespace . "/";
        }
        return static::$dir . $configName . ".php";
    }
}

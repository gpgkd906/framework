<?php

namespace Framework\Config;

use Framework\Config\ConfigModel\ConfigModelInterface;
use Framework\Config\ConfigModel\AbstractConfigModel;

class ConfigModel extends AbstractConfigModel implements ConfigModelInterface 
{
    static protected function getFile($namespace, $configName)
    {
        if (static::$dir === null) {
            static::$dir = ROOT_DIR . "Framework/Config/" . $namespace . "/";
        }
        return static::$dir . $configName . ".php";
    }
}

<?php

namespace Framework\Config;

use Framework\Core\App;

use Framework\Core\Interfaces\ConfigModelInterface;

class ConfigModel implements ConfigModelInterface {
    
    static $instances = [];
    
    static $dir = null;

    static public function register($namespace, $configName)
    {
        if(!isset(self::$instances[$namespace])) {
            self::$instances[$namespace] = [];
        }
        if(!isset(self::$instances[$namespace][$configName])) {   
            $config = require(self::getFile($namespace, $configName));
            self::$instances[$namespace][$configName] = $config;
        }
        return self::$instances[$namespace][$configName];
    }

    static private function getFile($namespace, $configName)
    {
        if(self::$dir === null) {
            self::$dir = ROOT_DIR . "Framework/Config/" . $namespace . "/";
        }
        return self::$dir . $configName . ".php";
    }

    public function getConfig($key)
    {

    }
}

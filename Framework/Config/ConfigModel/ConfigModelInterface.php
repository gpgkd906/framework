<?php

namespace Framework\Config\ConfigModel;

interface ConfigModelInterface {
    
    static public function registerNamespace($namespace);
    
    static public function getConfigModel($config);

    public function getConfig($key);

    public function setConfig($key, $value);

    public function update();

    public function refresh();
}


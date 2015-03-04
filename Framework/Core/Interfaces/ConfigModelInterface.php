<?php

namespace Framework\Core\Interfaces;

interface ConfigModelInterface {
    
    static public function register($namespace, $config);

    public function getConfig($key);
}

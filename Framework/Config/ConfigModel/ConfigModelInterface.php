<?php
declare(strict_types=1);

namespace Framework\Config\ConfigModel;

interface ConfigModelInterface {

    static public function registerNamespace($namespace);

    static public function getConfigModel($config);

    public function get($key);

    public function set($key, $value);
}

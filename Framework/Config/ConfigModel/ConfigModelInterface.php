<?php
declare(strict_types=1);

namespace Framework\Config\ConfigModel;

interface ConfigModelInterface
{

    public static function registerNamespace($namespace);

    public static function getConfigModel($config);

    public function get($key);

    public function set($key, $value);
}

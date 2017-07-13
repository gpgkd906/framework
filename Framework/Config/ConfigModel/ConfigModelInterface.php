<?php

namespace Framework\Config\ConfigModel;

interface ConfigModelInterface {

    static public function registerNamespace($namespace);

    static public function getConfigModel($config);

    public function get($key);

    public function set($key, $value);

    public function update();

    public function refresh();
}

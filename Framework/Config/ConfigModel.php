<?php

namespace Framework\Config;

use Framework\Config\ConfigModel\ConfigModelInterface;
use Framework\Config\ConfigModel\AbstractConfigModel;

class ConfigModel extends AbstractConfigModel implements ConfigModelInterface
{
    static protected function getFile($namespace, $configName)
    {
        return ROOT_DIR . "Framework/Config/" . $namespace . "/" . $configName . ".php";
    }
}

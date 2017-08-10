<?php
declare(strict_types=1);

namespace Framework\Config;

use Framework\Config\ConfigModel\ConfigModelInterface;
use Framework\Config\ConfigModel\AbstractConfigModel;

class ConfigModel extends AbstractConfigModel implements ConfigModelInterface
{
    protected static function getFile($namespace, $configName)
    {
        return ROOT_DIR . "Framework/Config/" . $namespace . "/" . $configName . ".php";
    }
}

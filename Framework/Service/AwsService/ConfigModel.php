<?php

namespace Framework\Service\AwsService;

use Framework\Core\AbstractConfigModel;
use Exception;

class ConfigModel extends AbstractConfigModel
{
    static private $ConfigModel = null;

    static public function getConfigModel($config = null)
    {
        if (self::$ConfigModel === null) {
            self::$ConfigModel = parent::getConfigModel([
                "scope" => self::class
            ]);
        }
        return self::$ConfigModel;
    }
    
    static protected function getFile($namespace, $configName)
    {
        return ROOT_DIR . "Framework/Service/AwsService/config/config.php";
    }
}

<?php
define("ROOT_DIR", dirname(__FILE__) . "/../../");
define("ENVIRONMENT", "Development");
require ROOT_DIR . "vendor/autoload.php";

use Framework\Core\Console;
use Framework\Config\ConfigModel;

ConfigModel::registerNamespace(ENVIRONMENT);

$globalConfig = ConfigModel::getConfigModel([
    "scope" => ConfigModel::SUPER,
    "property" => ConfigModel::READONLY,
]);

Console::setGlobalConfig($globalConfig);
Console::run();

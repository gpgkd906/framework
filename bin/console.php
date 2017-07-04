<?php
define("ROOT_DIR", __DIR__ . "/../");
define("ENVIRONMENT", "Development");
require ROOT_DIR . "vendor/autoload.php";

use Framework\Application\ConsoleApplication;
use Framework\Config\ConfigModel;

ConfigModel::registerNamespace(ENVIRONMENT);

$globalConfig = ConfigModel::getConfigModel([
    "scope" => ConfigModel::SUPER,
    "property" => ConfigModel::READONLY,
]);

$Application = new ConsoleApplication($globalConfig);
$Application->run();

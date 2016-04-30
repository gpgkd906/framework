<?php

require './benchmark.class.php';
$bm = new benchmark;

define("ROOT_DIR", dirname(__FILE__) . "/");
define("ENVIRONMENT", "Development");
require ROOT_DIR . "vendor/autoload.php";

use Framework\Application\HttpApplication;
use Framework\Config\ConfigModel;

ConfigModel::registerNamespace(ENVIRONMENT);

$globalConfig = ConfigModel::getConfigModel([
    "scope" => ConfigModel::SUPER,
    "property" => ConfigModel::READONLY,
]);

$application = new HttpApplication($globalConfig);
$application->run();

$bm->display();

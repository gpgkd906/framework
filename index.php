<?php

define("ROOT_DIR", __DIR__ . "/");

require ROOT_DIR . 'Benchmark.php';
$bm = new Benchmark;

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

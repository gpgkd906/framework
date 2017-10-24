<?php
// @codingStandardsIgnoreFile
require __DIR__ . '/../globalConstant.php';
require ROOT_DIR . 'Benchmark.php';
$bm = new Benchmark;
require ROOT_DIR . "vendor/autoload.php";

use Framework\Application\HttpApplication;
use Std\Config\ConfigModel;

ConfigModel::registerNamespace(ENVIRONMENT);

$globalConfig = ConfigModel::getConfigModel([
    "scope" => ConfigModel::SUPER,
    "property" => ConfigModel::READONLY,
]);

$application = new HttpApplication($globalConfig);
$application->run();

$bm->display();

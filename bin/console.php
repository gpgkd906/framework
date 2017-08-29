<?php
// @codingStandardsIgnoreFile
require __DIR__ . '/../globalConstant.php';
require ROOT_DIR . 'Benchmark.php';
$bm = new Benchmark;
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

$bm->display();

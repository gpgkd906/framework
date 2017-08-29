<?php
// @codingStandardsIgnoreFile
require __DIR__ . '/../globalConstant.php';
require ROOT_DIR . "vendor/autoload.php";

use Framework\Config\ConfigModel;

ConfigModel::registerNamespace(ENVIRONMENT);
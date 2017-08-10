<?php
declare(strict_types=1);
namespace Framework\ErrorHandler;

use Framework\Config\ConfigModel;

$global = ConfigModel::getConfigModel([
    "scope" => ConfigModel::SUPER,
    "property" => ConfigModel::READONLY,
]);

if ($global->get("ErrorHandler", true)) {
    ErrorHandler::setup();
}

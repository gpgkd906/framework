<?php
declare(strict_types=1);
namespace Std\ErrorHandler;

use Std\Config\ConfigModel;

$global = ConfigModel::getConfigModel([
    "scope" => ConfigModel::SUPER,
    "property" => ConfigModel::READONLY,
]);

if ($global->get("ErrorHandler", true)) {
    ErrorHandler::setup();
}

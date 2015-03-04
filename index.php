<?php
define("ROOT_DIR", dirname(__FILE__) . "/");
require ROOT_DIR . "vendor/autoload.php";

use Framework\Core\App;
use Framework\Config\ConfigModel;

$config = ConfigModel::register("Development", "global");
/* $config = ConfigModel::register("Test", "global"); */
/* $config = ConfigModel::register("Production", "global"); */
App::run($config);


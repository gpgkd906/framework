<?php
/**
 * AppInterface
 *
 * [:package description]
 *
 * Copyright 2015 Chen Han
 *
 * Licensed under The MIT License
 *
 * @copyright Copyright 2015 Chen Han
 * @link
 * @since
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Framework\Core\Interfaces;

/**
 * AppInterface
 * [:class description]
 *
 * @author 2015 Chen Han
 * @package 
 * @link 
 */
interface AppInterface 
{
    static public function setGlobalConfig($globalConfig);

    static public function run();

    static public function getController($controllerName);

    static public function getModel($modelName);

    static public function getRouteModel($routeModelName);

    static public function getHelper($helperName);

    static public function getService($serviceName);

    static public function getPlugin($pluginName);

    static public function getModule($moduleName);

    static public function import($namespace, $className);    
}
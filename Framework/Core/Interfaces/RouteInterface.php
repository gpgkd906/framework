<?php

/**
 * RouteInterface
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
namespace FrameWork\Core\Interfaces;

/**
 * RouteInterface
 * [:class description]
 *
 * @author 2015 Chen Han
 * @package 
 * @link 
 */
class RouteInterface 
{
    public static function getSingleton();

    public function addAppTable($appUrl);

    public function setAppTable($appTable);

    public function mapping();

    public function dispatch();

    public function redirect($app);

    public function badRequest();

    public function notFound();

    public function forbidden();

    public function serverError();
}

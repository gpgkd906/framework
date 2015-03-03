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
namespace FrameWork\Core;

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
    public static function getSingletonInstance();

    public function add();

    public function regularParse();
    
    public function mapping();

    public function request();

    public function redirect();

    public function badRequest();

    public function notFound();

    public function forbidden();

    public function serverError();
}

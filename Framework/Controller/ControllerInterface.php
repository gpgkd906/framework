<?php

/**
 * ControllerInterface
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
namespace Framework\Controller;

/**
 * ControllerInterface
 * [:class description]
 *
 * @author 2015 Chen Han
 * @package
 * @link
 */
interface ControllerInterface
{
    public function callActionFlow($action, $restAction);

    public function response();

    public static function getDescription();
}

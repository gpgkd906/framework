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
namespace Framework\Core\Interface;

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
    public function process($request);

    private function callAction($action, $restAction);

    public function setResponseType();

    public function getResponseType();
    
    public function response();

    protected function responseHtml();

    protected function responseJSON();

    protected function responseXML();
}

<?php

/**
 * FrameWork\Core
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
 * ViewModelInterface
 * [:class description]
 *
 * @author 2015 Chen Han
 * @package 
 * @link 
 */
interface ViewModelInterface 
{    
    public function setTemplate($template);

    public function getTemplate();

    public function setData($data);

    public function getData();
   
    public function asHtml();
    
    public function asJson();
    
    public function asXml();

    public function render();
    
    public function renderAsHtml();
    
    public function renderAsJson();
    
    public function renderAsXml();
}

<?php

namespace Framework\Console;

use Framework\Core\AbstractConsole;
use Framework\Core\App;

class TemplateController extends AbstractConsole
{
    public function index()
    {
        $Template = App::getService("Template");
        $file = "/home/chen/framework/Framework/Console/test.html";
        $content = file_get_contents($file);
        $res = $Template->getParser()->parse($content);
        var_dump($res);
    }    
}

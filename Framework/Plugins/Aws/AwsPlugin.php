<?php

namespace Framework\Plugins\Aws;

use Framework\Core\PluginManager\AbstractPlugin;

class AwsPlugin extends AbstractPlugin
{
    public $actions = [
        "PluginInited" => "onloaded",
    ];
    
    public function onloaded()
    {
        var_dump("Aws onloaded");
    }
}

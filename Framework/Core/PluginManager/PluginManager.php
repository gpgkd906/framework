<?php
namespace Framework\Core\PluginManager;

use Framework\Core\Interfaces\EventInterface;
use Framework\Config\ConfigModel;

class PluginManager implements EventInterface
{
    use \Framework\Core\EventManager\EventTrait;
    
    private $config = null;
    private $plugins = [];
    static private $instance = null;

    static public function getSingleton()
    {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->config = ConfigModel::getConfigModel([
            "scope" => ConfigModel::PLUGINS,
            "property" => ConfigModel::READONLY
        ]);
    }
    
    public function initPlugins()
    {
        foreach($this->config->getConfig("plugins", []) as $pluginConfig) {
            if(isset($pluginConfig["enabled"]) && $pluginConfig["enabled"]) {
                $pluginLabel = $pluginConfig["identify"];
                $plugin = $pluginLabel::getSingleton();
                $this->plugins[$pluginLabel] = $plugin;
                $plugin->init($this);
            }
        }
        $this->triggerEvent("PluginInited");
    }
    
}

<?php
namespace Framework\Plugin\Plugin;

use Framework\Event\Event\EventInterface;
use Framework\Config\ConfigModel;

class PluginManager implements EventInterface
{
    use \Framework\Event\Event\EventTrait;
    use \Framework\Application\SingletonTrait;
    
    const TRIGGER_PLUGININITED = "PluginInited";
    
    private $config = null;
    private $plugins = [];

    private function __construct()
    {
        $this->config = ConfigModel::getConfigModel([
            "scope" => ConfigModel::PLUGINS,
            "property" => ConfigModel::READWRITE
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
        $this->triggerEvent(self::TRIGGER_PLUGININITED);
    }
    
    public function isInstalledPlugin($plugin)
    {
        
    }
    
    public function installPlugin($plugin)
    {
         if($this->isInstalledPlugin($plugin)) {
             return $this->updatePlugin($plugin);
         }

     }

    public function updatePlugin($plugin)
    {
        $pluginName = $plugin->getName();
        $installedPlugin = $this->config->getConfig("plugins");
    }
}

<?php

namespace Framework\Application;

use Framework\Config\ConfigModel;
use Exception;

class ServiceManager implements ServiceManagerInterface
{
    use SingletonTrait;

    const ConfigModel = 'ClassLoader';
    
    /**
     *
     * @api
     * @var mixed $application 
     * @access private
     * @link
     */
    private $application = null;

    private $config = null;

    private function setConfig($config)
    {        
        $this->config = $config;
    }

    private function getConfig()
    {
        if($this->config === null) {
            $this->setConfig(
                ConfigModel::getConfigModel([
                    "scope" => static::ConfigModel,
                    "property" => ConfigModel::READONLY,
                ])
            );
        }
        return $this->config;
    }
    
    public function getService($type, $name)
    {
        $config = $this->getConfig();
        $loadInfo = $config->getConfig($type);

        $Namespace = isset($loadInfo['namespace']) ? $loadInfo['namespace'] : $type;
        $isSingleton = isset($loadInfo['isSingleton']) ? $loadInfo['isSingleton'] : false;
        if(isset($loadInfo['Factory'])) {
            $Factory = $loadInfo['Factory'];
            return $Factory::MakeObject($name);
        } else {
            $Class = $Namespace . '\\' . $name;
            if(isset($loadInfo['classes'])
            && isset($loadInfo['classes'][$name])) {
                if(isset($loadInfo['classes'][$name]['Class'])) {
                    $Class = $loadInfo['classes'][$name]['Class'];
                }
                if(isset($loadInfo['classes'][$name]['isSingleton'])) {
                    $isSingleton = $loadInfo['classes'][$name]['isSingleton'];
                }
            }
            if($isSingleton) {
                return $Class::getSingleton();
            } else {
                return new $Class;
            }
        }
    }

    public function getComponent($type, $name)
    {
        $name = ucfirst($name) . ucfirst($type);
        $Component = $this->getService($type, $name);
        $Component->setServiceManager($this);
        return $Component;
    }
    
    public function getServiceNamespace($type)
    {
        $config = $this->getConfig();
        $loadInfo = $config->getConfig($type);

        return isset($loadInfo['namespace']) ? $loadInfo['namespace'] : $type;
    }

    public function getModel($model)
    {
        $model = $model . '\Model';
        return $this->getService("Model", $model);
    }

    public function getController($controller)
    {
        return $this->getComponent('Controller', $controller);
    }

    /**
     * 
     * @api
     * @param mixed $application
     * @return mixed $application
     * @link
     */
    public function setApplication ($application)
    {
        return $this->application = $application;
    }

    /**
     * 
     * @api
     * @return mixed $application
     * @link
     */
    public function getApplication ()
    {
        return $this->application;
    }    
}

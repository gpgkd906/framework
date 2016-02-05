<?php

namespace Framework\Application;

use Framework\Application\ServiceManagerAwareInterface;
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

    public function get($type, $name)
    {
        $config = $this->getConfig();
        $loadInfo = $config->getConfig($type);

        $Namespace = 'Framework\\' . $type;
        $isSingleton = isset($loadInfo['isSingleton']) ? $loadInfo['isSingleton'] : false;
        if(isset($loadInfo['Factory'])) {
            $Factory = $loadInfo['Factory'];
            return $Factory::getService($name);
        } else {
            $Class = $Namespace . '\\' . $name;
            if(!class_exists($Class)) {
                $Class = $Class . '\\' . $name;
            }
            var_dump($Class);
            if(!class_exists($Class)) {
                return false;
            }
            if($isSingleton) {
                $Service = $Class::getSingleton();
            } else {
                $Service = new $Class;
            }
            if($Service instanceof ServiceManagerAwareInterface) {
                $Service->setServiceManager($this);
            }
            return $Service;
        }
    }

    public function getComponent($type, $name)
    {
        $name = ucfirst($name) . ucfirst($type);
        $Component = $this->get($type, $name);
        return $Component;
    }
    
    public function getServiceNamespace($type)
    {
        $config = $this->getConfig();
        $loadInfo = $config->getConfig($type);

        return isset($loadInfo['namespace']) ? $loadInfo['namespace'] : $type;
    }
    
    public function getController($controller)
    {
        return $this->getComponent('Controller', $controller);
    }

    public function getSessionService()
    {
        return $this->get('Service', 'SessionService');
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

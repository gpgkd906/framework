<?php

namespace Framework\Application;

trait ServiceManagerAwareTrait
{
    private $serviceManager = null;

    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}

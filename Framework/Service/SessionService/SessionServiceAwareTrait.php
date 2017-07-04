<?php

namespace Framework\Service\SessionService;

trait SessionServiceAwareTrait
{
    private $SessionService;
    
    public function setSessionService(SessionService $SessionService)
    {
        $this->SessionService = $SessionService;
    }

    public function getSessionService()
    {
        return $this->SessionService;
    }
}

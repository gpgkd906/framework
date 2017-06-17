<?php

namespace Framework\Router;

trait RouterAwareTrait
{
    private $Router;

    public function setRouter(RouterInterface $Router)
    {
        $this->Router = $Router;
    }

    public function getRouter()
    {
        return $this->Router;
    }
}

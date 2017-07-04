<?php

namespace Framework\Router;

interface RouterAwareInterface
{
    public function setRouter(RouterInterface $Router);
    public function getRouter();
}

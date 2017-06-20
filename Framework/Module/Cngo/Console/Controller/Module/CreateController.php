<?php

namespace Framework\Module\Cngo\Console\Controller\Module;

use Framework\Controller\Controller\AbstractConsole;
use Zend\EventManager\EventManagerAwareInterface;

class CreateController extends AbstractConsole
{
    public function index()
    {
    }

    public function getDescription()
    {
        return 'module generator';
    }

    public function getHelp()
    {
        return 'need some help';
    }
}

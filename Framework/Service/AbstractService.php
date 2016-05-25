<?php

namespace Framework\Service;

use Framework\Application\SingletonInterface;
use Framework\Application\ServiceManagerAwareInterface;

class AbstractService implements SingletonInterface, ServiceManagerAwareInterface
{
    use \Framework\Application\SingletonTrait;
    use \Framework\Application\ServiceManagerAwareTrait;
}

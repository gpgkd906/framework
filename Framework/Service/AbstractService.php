<?php

namespace Framework\Service;

use Framework\ObjectManager\SingletonInterface;
use Framework\ObjectManager\ObjectManagerAwareInterface;

class AbstractService implements SingletonInterface, ObjectManagerAwareInterface
{
    use \Framework\ObjectManager\SingletonTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
}

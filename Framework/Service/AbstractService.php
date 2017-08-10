<?php
declare(strict_types=1);

namespace Framework\Service;

use Framework\ObjectManager\SingletonInterface;
use Framework\ObjectManager\ObjectManagerAwareInterface;

class AbstractService implements SingletonInterface, ObjectManagerAwareInterface
{
    use \Framework\ObjectManager\SingletonTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
}

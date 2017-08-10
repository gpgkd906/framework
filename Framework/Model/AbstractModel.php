<?php
declare(strict_types=1);

namespace Framework\Model;

use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ObjectManager\SingletonInterface;

abstract class AbstractModel implements ModelInterface, ObjectManagerAwareInterface, SingletonInterface
{
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\ObjectManager\SingletonTrait;

    abstract public function getEntities();
}

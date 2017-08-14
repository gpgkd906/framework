<?php
declare(strict_types=1);
namespace Framework\Repository\Doctrine;

use Framework\Repository\EntityManagerAwareInterface;
use Framework\ObjectManager\ObjectManager;
use Framework\ModelManager\AbstractModel;

class AbstractEntity extends AbstractModel implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    
}

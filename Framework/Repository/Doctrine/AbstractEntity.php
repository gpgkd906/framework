<?php
declare(strict_types=1);
namespace Framework\Repository\Doctrine;

use Framework\Repository\EntityManagerAwareInterface;
use Framework\ObjectManager\ObjectManager;

class AbstractEntity implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    private static $entityKey = [];

    public function toArray()
    {
        $entityArray = array_values((array) $this);
        $entityKey = $this->getEntityKey();
        return array_combine($entityKey, $entityArray);
    }

    public function fromArray($data)
    {
        $entityKey = $this->getEntityKey();
        foreach ($entityKey as $key) {
            if (isset($data[$key])) {
                $setter = [$this, 'set' . ucfirst($key)];
                if (is_callable($setter)) {
                    call_user_func($setter, $data[$key]);
                }
            }
        }
    }

    private function getEntityKey()
    {
        if (!isset(self::$entityKey[static::class])) {
            self::$entityKey[static::class] = array_map(function ($item) {
                return trim(str_replace([self::class, static::class], '', $item));
            }, array_keys((array) $this));
        }
        return self::$entityKey[static::class];
    }
}

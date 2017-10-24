<?php
declare(strict_types=1);

namespace Std\ModelManager;

abstract class AbstractModel implements ModelInterface
{
    private static $modelKey = [];

    public function toArray()
    {
        $modelArray = array_values((array) $this);
        $modelKey = $this->getModelKey();
        return array_combine($modelKey, $modelArray);
    }

    public function fromArray($data)
    {
        $modelKey = $this->getModelKey();
        foreach ($modelKey as $key) {
            if (isset($data[$key])) {
                $setter = [$this, 'set' . ucfirst($key)];
                if (is_callable($setter)) {
                    call_user_func($setter, $data[$key]);
                }
            }
        }
    }

    private function getModelKey()
    {
        if (!isset(self::$modelKey[static::class])) {
            self::$modelKey[static::class] = array_map(function ($item) {
                return trim(str_replace([self::class, static::class], '', $item));
            }, array_keys((array) $this));
        }
        return self::$modelKey[static::class];
    }
}

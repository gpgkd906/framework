<?php
declare(strict_types=1);

namespace Std\ModelManager;
use Framework\ObjectManager\SingletonInterface;

class ModelManager implements ModelManagerInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    public function getModel($ModelClass, $record = null)
    {
        $Model = new $ModelClass;
        if ($record) {
            $Model->fromArray($record);
        }
        return $Model;
    }

    public function getIterator($ModelClass, $data)
    {
        return new ModelIterator($ModelClass, $data);
    }
}

<?php
declare(strict_types=1);

namespace Framework\ValidatorManager;

interface ValidatorManagerInterface
{
    public static function getSingleton();

    public function createInputFilter($inputFilter);

    public function createValidator($validators);
}

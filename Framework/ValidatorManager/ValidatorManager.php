<?php

namespace Framework\ValidatorManager;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Framework\ObjectManager\SingletonInterface;

class ValidatorManager implements ValidatorManagerInterface, SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    public function createInputFilter($inputFilter = null)
    {
        $InputFilter = new InputFilter();
        if ($inputFilter) {
            $InputFilter->add($inputFilter);
        }
        return $InputFilter;
    }

    public function createValidator($validators)
    {
        throw new \Exception("Not implements");
    }
}

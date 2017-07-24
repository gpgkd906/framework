<?php

namespace Framework\ValidatorManager;

interface ValidatorManagerAwareInterface
{
    public function setValidatorManager(ValidatorManagerInterface $ValidatorManager);
    public function getValidatorManager();
}

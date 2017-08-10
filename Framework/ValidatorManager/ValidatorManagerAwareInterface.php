<?php
declare(strict_types=1);

namespace Framework\ValidatorManager;

interface ValidatorManagerAwareInterface
{
    public function setValidatorManager(ValidatorManagerInterface $ValidatorManager);
    public function getValidatorManager();
}

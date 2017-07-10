<?php

namespace Framework\Module\Cngo\Console\Helper;

trait ConsoleHelperAwareTrait
{
    private $ConsoleHelper;

    public function setConsoleHelper(ConsoleHelperInterface $ConsoleHelper)
    {
        $this->ConsoleHelper = $ConsoleHelper;
    }

    public function getConsoleHelper()
    {
        return $this->ConsoleHelper;
    }
}

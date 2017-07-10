<?php

namespace Framework\Module\Cngo\Console\Helper;

interface ConsoleHelperAwareInterface
{
    public function setConsoleHelper(ConsoleHelperInterface $ConsoleHelper);
    public function getConsoleHelper();
}

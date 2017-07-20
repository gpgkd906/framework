<?php

namespace Framework\Module\Cngo\Console\Helper\Console;

interface ConsoleHelperAwareInterface
{
    public function setConsoleHelper(ConsoleHelperInterface $ConsoleHelper);
    public function getConsoleHelper();
}

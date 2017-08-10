<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Console\Helper\Console;

interface ConsoleHelperAwareInterface
{
    public function setConsoleHelper(ConsoleHelperInterface $ConsoleHelper);
    public function getConsoleHelper();
}

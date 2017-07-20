<?php

namespace Framework\Module\Cngo\Console\Helper\Console;

interface ConsoleHelperInterface
{
    public function confirm($question, bool $default = null, array $confirmations = null);

    public function ask($question, string $default = null);

    public function choice($question, array $choices, $default = null);
}

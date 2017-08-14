<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Console\Helper\Console;

class ConsoleHelper implements ConsoleHelperInterface
{
    public function confirm($question, bool $default = null, array $confirmations = null)
    {
        if ($confirmations === null) {
            $confirmations = ['y', 'Y'];
        }
        if ($default === null) {
            $default = true;
        }
        do {
            $input = readline($question . '? ');
            if (!in_array($input, $confirmations)) {
                $input = null;
            } else {
                $input = true;
            }
            if ($input === null && $default !== null) {
                $input = $default;
                break;
            }
        } while ($input === null);
        // readline_add_history($input);
        return $input;
    }

    public function ask($question, string $default = null)
    {
        do {
            $input = readline($question . '? ');
            if (!$input && $default !== null) {
                $input = $default;
                break;
            }
        } while (!$input);
        readline_add_history($input);
        return trim($input);
    }

    public function choice($question, array $choices, $default = null)
    {
        $question = $question . '?' . '[' . join('/', $choices) . ']';
        do {
            $input = readline($question . '? ');
            if (!in_array($input, $choices)) {
                $input = null;
            }
            if (!$input && $default !== null && isset($choices[$default])) {
                $input = $choices[$default];
                break;
            }
        } while (!$input);
        readline_add_history($input);
        return trim($input);
    }
}

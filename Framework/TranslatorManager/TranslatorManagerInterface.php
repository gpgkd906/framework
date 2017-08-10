<?php
declare(strict_types=1);

namespace Framework\TranslatorManager;

interface TranslatorManagerInterface
{
    const VALIDATOR = 'validator';

    public static function getSingleton();

    public function getTranslator($type);
}

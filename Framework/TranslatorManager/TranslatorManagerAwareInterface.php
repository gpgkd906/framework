<?php
declare(strict_types=1);

namespace Framework\TranslatorManager;

interface TranslatorManagerAwareInterface
{
    public function setTranslatorManager(TranslatorManagerInterface $TranslatorManager);
    public function getTranslatorManager();
}

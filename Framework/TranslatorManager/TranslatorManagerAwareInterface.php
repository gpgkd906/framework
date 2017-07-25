<?php

namespace Framework\TranslatorManager;

interface TranslatorManagerAwareInterface
{
    public function setTranslatorManager(TranslatorManagerInterface $TranslatorManager);
    public function getTranslatorManager();
}

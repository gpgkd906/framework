<?php

namespace Framework\Core\Interfaces;

interface ViewModelManagerInterface
{
    static public function getViewModel($viewModelConfig);

    static public function setNamespace($namespace);

    static public function setTemplateDir($templateDir);
}

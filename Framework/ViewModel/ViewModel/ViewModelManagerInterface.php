<?php

namespace Framework\ViewModel\ViewModel;

interface ViewModelManagerInterface
{
    static public function getViewModel($viewModelConfig);

    static public function setNamespace($namespace);

    static public function setTemplateDir($templateDir);
}

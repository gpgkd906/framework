<?php

namespace Framework\ViewModel;

interface ViewModelManagerInterface
{
    static public function getViewModel($viewModelConfig);

    static public function setTemplateDir($templateDir);
}

<?php
declare(strict_types=1);

namespace Framework\ViewModel;

interface ViewModelManagerInterface
{
    static public function getViewModel($viewModelConfig);

    static public function setTemplateDir($templateDir);
}

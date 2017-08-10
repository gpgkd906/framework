<?php
declare(strict_types=1);

namespace Framework\ViewModel;

interface ViewModelManagerInterface
{
    public static function getViewModel($viewModelConfig);

    public static function setTemplateDir($templateDir);
}

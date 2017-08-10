<?php
declare(strict_types=1);

namespace Framework\ViewModel;

interface LayoutInterface
{
    public function registerStyle($style, $priority);

    public function registerScript($script, $priority);

    public function getStyle();

    public function getScript();

    public function setAsset($asset);

    public function getAsset();
}

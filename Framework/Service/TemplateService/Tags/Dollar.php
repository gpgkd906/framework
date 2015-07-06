<?php

namespace Framework\Service\TemplateService\Tags;

use Framework\Service\TemplateService\Parser\AbstractTag;

class Dollar extends AbstractTag
{

    public function onParse($Parser)
    {
        var_dump($this);
        die;
    }
}
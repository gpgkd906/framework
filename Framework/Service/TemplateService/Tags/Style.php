<?php

namespace Framework\Service\TemplateService\Tags;

use Framework\Service\TemplateService\Parser\AbstractTag;

class Style extends AbstractTag
{
    const isWrapTag = true;
    
    public function onParse($Parser)
    {
        return $this;
    }
}


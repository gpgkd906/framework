<?php

namespace Framework\Service\TemplateService\Tags;

use Framework\Service\TemplateService\Parser\AbstractTag;

class Page extends AbstractTag
{
    const isGlobalTag = true;

    public function onParse($Parser)
    {
        return $this;
    }
}


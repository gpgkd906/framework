<?php

namespace Framework\Service\TemplateService\Tags;

use Framework\Service\TemplateService\Parser\AbstractTag;

class Setf extends AbstractTag
{
    const isSingleTag = true;

    public function onParse($Parser)
    {
        $data = $this->getAttrs();
        $data = array_merge($Parser->getData(), $data);
        $Parser->setData($data);
        return null;
    }
}


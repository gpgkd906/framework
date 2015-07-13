<?php

namespace Framework\Service\TemplateService\Parser;

use Framework\Service\TemplateService\Parser\Interfaces\TagInterface;

class Expression extends AbstractTag
{
    const isSingleTag = false;

    public function onParse($Parser) {
        $delimiter = $Parser->getDelimiter();
        $raw = trim(str_replace($delimiter, "", $this->getRaw()));
        $raw = $this->convertPipe($raw);
        $raw = $ths->convertDot($raw);
    }

    public function convertPipe($raw)
    {
        
    }
}

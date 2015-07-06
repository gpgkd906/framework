<?php

namespace Framework\Service\TemplateService;
use Framework\Service\TemplateService\Parser\Parser;
use Framework\Service\TemplateService\Tags;

class Engine extends Parser
{
    public $tag = [
        "page" => Tags\Page::class,
        "block"    => Tags\Block::class,
        "common"   => Tags\Common::class,
        "parts"    => Tags\Parts::class,
        "script"   => Tags\Script::class,
        "style"    => Tags\Style::class,
    ];
}
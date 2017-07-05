<?php

namespace Framework\Service\TemplateService\Tags;

use Framework\Service\TemplateService\Parser\AbstractTag;

class Style extends AbstractTag
{
    const isWrapTag = true;
    
    /**
     *
     * @api
     * @var mixed $css 
     * @access private
     * @link
     */
    private $css = [];

    /**
     * 
     * @api
     * @param mixed $css
     * @return mixed $css
     * @link
     */
    public function setCss ($css)
    {
        return $this->css = $css;
    }

    /**
     * 
     * @api
     * @return mixed $css
     * @link
     */
    public function getCss ()
    {
        return $this->css;
    }

    public function onParse($Parser)
    {
        $content = $this->getContent();
        $set = explode(PHP_EOL, $content);
        $css = [];
        foreach ($set as $link) {
            $link = trim(str_replace(['\'', '"'], '', $link));
            $data = $Parser->getTagInfo($link, "<", "/>");
            if (isset($data["rel"]) && $data["rel"] === "stylesheet") {
                $css[] = $data["href"];
            }
        }
        $this->setCss($css);
        return $this;
    }
}


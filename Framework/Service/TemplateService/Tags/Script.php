<?php

namespace Framework\Service\TemplateService\Tags;

use Framework\Service\TemplateService\Parser\AbstractTag;

class Script extends AbstractTag
{
    const isWrapTag = true;

    /**
     *
     * @api
     * @var mixed $js 
     * @access private
     * @link
     */
    private $js = [];

    /**
     * 
     * @api
     * @param mixed $js
     * @return mixed $js
     * @link
     */
    public function setJs ($js)
    {
        return $this->js = $js;
    }

    /**
     * 
     * @api
     * @return mixed $js
     * @link
     */
    public function getJs ()
    {
        return $this->js;
    }
    
    public function onParse($Parser)
    {
        $content = $this->getContent();
        $set = explode(PHP_EOL, $content);
        $js = [];
        foreach ($set as $script) {
            $script = trim(str_replace(['\'', '"'], '', $script));
            $data = $Parser->getTagInfo($script, "<script", "/>");
            if (isset($data["src"])) {
                $js[] = $data["src"];
            }
        }
        $this->setJs($js);
        return $this;
    }
}


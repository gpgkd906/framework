<?php

require "./Parser/Parser.php";

class TemplateService extends Parser
{
    public function tagBlock($tagInfo)
    {
        $tagInfo["replace"] = '$element' . $this->getId();
        return $tagInfo;
    }

    public function tagParts($tagInfo)
    {
        $tagInfo["replace"] = '$element' . $this->getId();
        return $tagInfo;
    }

}
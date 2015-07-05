<?php

namespace Framework\Service\TemplateService;

use Framework\Core\AbstractService;

class TemplateService extends AbstractService
{
    /**
     *
     * @api
     * @var mixed $parser 
     * @access private
     * @link
     */
    private $parser = null;

    /**
     * 
     * @api
     * @return mixed $parser
     * @link
     */
    public function getParser ()
    {
        if($this->parser === null) {
            $this->parser = new Engine;
        }
        return $this->parser;
    }
}
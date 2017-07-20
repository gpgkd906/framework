<?php

namespace Framework\Module\Cngo\Console\Helper\Generator;

trait GeneratorAwareTrait
{
    private $Generator;

    public function setGenerator(GeneratorInterface $Generator)
    {
        $this->Generator = $Generator;
    }

    public function getGenerator()
    {
        return $this->Generator;
    }
}

<?php
declare(strict_types=1);

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

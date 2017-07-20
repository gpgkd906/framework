<?php

namespace Framework\Module\Cngo\Console\Helper\Generator;

interface GeneratorAwareInterface
{
    public function setGenerator(GeneratorInterface $Generator);
    public function getGenerator();
}

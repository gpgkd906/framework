<?php
declare(strict_types=1);

namespace Framework\ViewModel;

interface ViewModelInterface
{
    public function setTemplate($template);

    public function getTemplate();

    public function setData($data);

    public function getId();

    public function getData();

    public function render();
}

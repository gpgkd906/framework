<?php

namespace Framework\ViewModel;

interface ViewModelInterface
{
    public function setTemplate($template);

    public function getTemplate();

    public function setData($data);

    public function getId();

    public function getData();

    public function asHtml();

    public function asJson();

    public function render();
}

<?php
declare(strict_types=1);
namespace Std\FormManager\Element;

class Hidden extends FormElement
{

    public function makeConfirm($value)
    {
        $name = $this->getElementName();
        return "<label class='form_label form_{$this->type}'><input type='hidden' name='{$name}' value='{$value}'></label>";
    }
}

<?php
declare(strict_types=1);
namespace Std\FormManager\Element;

class Password extends FormElement
{

    /**
    * 一般要素のインプットモード
    * @param string/integer $value 要素値
    * @param array 要素の属性
    * @return
    */
    public function makeInput($value = null, $attr = null)
    {
        if ($value === null) {
            $value = $this->val;
        }
        $name = $this->getElementName();
        $html ="<input type='{$this->type}' name='{$name}' value='{$value}' {$attr}>";
        return $html;
    }

    /**
    * パスワードの確認モード
    * @param string/integer $value 要素値
    * @return
    */
    public function makeConfirm($value)
    {
        $name = $this->getElementName();
        return "<label class='form_label form_{$this->type}'><input type='hidden' name='{$name}' value='{$value}'>" . str_pad("", strlen($value), "*") . "</label>";
    }
}

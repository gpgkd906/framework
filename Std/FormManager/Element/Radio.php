<?php
declare(strict_types=1);
namespace Std\FormManager\Element;

class Radio extends FormElement
{

    /**
    * radioのインプットモード
    * @param string/integer $value 要素値
    * @param array 要素の属性
    * @return
    */
    public function makeInput($value = null, $attr = null)
    {
        $html = array();
        $name = $this->getElementName();
        foreach ($this->val as $val => $key) {
            if ($value !== null && $val == $value) {
                $html[] = "<label class='form_label form_radio'><input type='radio' name='{$name}' value='{$val}' {$attr} checked>" . $key . "</label>";
            } else {
                $html[] = "<label class='form_label form_radio'><input type='radio' name='{$name}' value='{$val}' {$attr}>" . $key . "</label>";
            }
        }
        return join("", $html);
    }

    /**
    * 複数候補の単一選択要素の確認モード
    * @param string/integer $value 要素値
    * @return
    */
    public function makeConfirm($value)
    {
        $html = "";
        $name = $this->getElementName();
        foreach ($this->val as $val => $key) {
            if ($value !== null && $val == $value) {
                $html = "<label class='form_label form_{$this->type}'><input type='hidden' name='{$name}' value='{$value}'>" . nl2br($key) . "</label>";
                break;
            }
        }
        return $html;
    }
}

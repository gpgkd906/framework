<?php
declare(strict_types=1);
namespace Std\FormManager\Element;

use Zend\InputFilter\InputFilter;
use Std\FormManager\FormManager;
use Std\TranslatorManager\TranslatorManagerAwareInterface;
use Std\TranslatorManager\TranslatorManagerInterface;

//负责表示要素对象
class FormElement implements FormElementInterface
{
    use \Std\TranslatorManager\TranslatorManagerAwareTrait;

    /**
    * フォームインスタンスの参照
    * @var resource
    * @link http://
    */
    protected $form = null;

    /**
    * 要素名
    * @var string
    * @link http://
    */
    protected $name = null;

    /**
    * 要素タイプ
    * @var string
    * @link http://
    */
    protected $type = null;

    /**
    * 要素値(候補含む、checkbox,radio,selectなど)
    * @var resource
    * @link http://
    */
    protected $val = null;

    /**
    * 要素値
    * @var mix
    * @link http://
    */
    private $value = null;

    /**
    * html出力する時のタグname
    * @var mix
    * @link http://
    */
    protected $elementName = null;
    /**
    * 出力モード
    * @var string
    * @link http://
    */
    protected $mode = "input";

    /**
    * バリデーションルールキュー
    * @var array
    * @link http://
    */
    protected $InputFilter = array();

    /**
    * バリデーションエラーメッセージ
    * @var string
    * @link http://
    */
    public $error = "";

    /**
    * 要素の属性
    * @var array
    * @link http://
    */
    protected $attrs = array();

    /**
    *
    * @api
    * @var mixed $scope
    * @access private
    * @link
    */
    private $scope = null;

    /**
    *
    * @api
    * @param mixed $scope
    * @return mixed $scope
    * @link
    */
    public function setScope($scope)
    {
        return $this->scope = $scope;
    }

    /**
    *
    * @api
    * @return mixed $scope
    * @link
    */
    public function getScope()
    {
        return $this->scope;
    }

    /**
    * 要素の属性アクセサー、値の設定または参照
    * @param string $name 属性名
    * @param array $value 属性値
    * @return
    */
    public function __call($name, $value)
    {
        if (empty($value)) {
            return $this->get($name);
        } else {
            return $this->set($name, $value[0]);
        }
    }

    /**
    * 要素の属性アクセサー、値の設定
    * @param string $name 属性名
    * @param array $value 属性値
    * @return
    */
    public function set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        } else {
            $this->attrs[$name] = $value;
        }
        return $this;
    }

    /**
    * 要素の属性アクセサー、値の参照
    * @param string $name 属性名
    * @return
    */
    public function get($name)
    {
        if (isset($this->attrs[$name])) {
            return $this->attrs[$name];
        } elseif (isset($this->{$name})) {
            return $this->{$name};
        } elseif ($name === "value") {
            return $this->getValue();
        }
    }

    /**
    *
    * @api
    * @param mixed $form
    * @return mixed $form
    * @link
    */
    public function setForm($form)
    {
        return $this->form = $form;
    }

    /**
    *
    * @api
    * @return mixed $form
    * @link
    */
    public function getForm()
    {
        return $this->form;
    }

    /**
    * 要素のclass追加
    * @param array $class class名
    * @return
    */
    public function addClass($class)
    {
        $cls = explode(" ", $this->get("class"));
        if (!in_array($class, $cls)) {
            $cls[] = $class;
        }
        $cls = join(" ", $cls);
        $this->set("class", $cls);
        return $this;
    }

    /**
    * 要素のclass削除
    * @param array $class class名
    * @return
    */
    public function removeClass($class)
    {
        $cls = explode(" ", $this->get("class"));
        if (in_array($class, $cls)) {
            $cls = array_diff($cls, array($class));
        }
        $cls = join(" ", $cls);
        $this->set("class", $cls);
        return $this;
    }

    /**
    * 要素値の参照
    * @return
    */
    protected function getValue()
    {
        if ($this->value === null) {
            $this->value = $this->getForm()->getData($this->getScope(), $this->name, true);
        }
        return $this->value;
    }

    /**
    * 要素値の廃棄
    * @return
    */
    public function clear()
    {
        $this->value = null;
    }

    /**
    * 要素の生成
    * @param object $form 親フォームのインスタンス参照
    * @param string $name 要素名
    * @param integer $type 要素タイプ
    * @param mix $val 要素値(checkbox, radio, selectなど用)
    * @param string/integer $default 要素の初期値
    * @return
    */
    public function __construct($name, $type = null, $val = null, $default = null)
    {
        $this->name = $name;
        $this->type = $type ?? $this->type;
        $this->val = $val ?? $this->val;
        if ($default !== null) {
            $this->value = $default;
        }
    }

    /**
    * バリデーションルール設定
    * @param integer $rule バリデーションチェッカールール値
    * @param string $message エラーメッセージ
    * @return this
    */
    public function addValidator(InputFilter $InputFilter)
    {
        $this->InputFilter = $InputFilter;
        return $this;
    }

    /**
    * バリデーションルールを解除する
    * @param integer $rule バリデーションチェッカールール値
    * @return this
    */
    public function removeValidator($name)
    {
        $this->InputFilter->remove($name);
        return $this;
    }

    /**
     * 全てのバリデーションルールを解除する
     *
     * @return this
     */
    public function clearValidator()
    {
        $this->InputFilter = null;
        return $this;
    }

    /**
    * バリデーション処理
    * @return
    */
    public function isValid()
    {
        if (!$this->InputFilter) {
            return true;
        }
        $data = $this->getForm()->getData($this->getScope());
        $this->InputFilter->setData($data);
        $isValid = $this->InputFilter->isValid();
        if (!$isValid) {
            $translator = $this->getTranslatorManager()->getTranslator(TranslatorManagerInterface::VALIDATOR);
            $message = array_map([$translator, 'translate'], $this->InputFilter->getMessages()[$this->name]);
            $message = nl2br(join(PHP_EOL, $message));
            $this->error = "<span class='form_error'>$message</span>";
        }
        return $isValid;
    }

    /**
    * 要素を強制的にエラーにする
    * @param string $message
    * @return
    */
    public function forceError($message = null)
    {
        $this->error = "<span class='form_error'>" . $message . "</span>";
        if ($this->getForm()) {
            $this->getForm()->forceError();
        }
        return $this;
    }

    /**
    * 要素を確認モードにする
    * @return
    */
    public function confirmMode()
    {
        $this->mode = "confirm";
        return $this;
    }

    /**
    * 要素をインプットモードにする
    * @return
    */
    public function inputMode()
    {
        $this->mode = "input";
        return $this;
    }

    /**
    * 要素を出力する
    * @return
    */
    public function __toString()
    {
        $value = $this->getValue();
        $value = FormManager::escape($value);
        $attrs = FormManager::attrFormat($this->attrs);
        switch ($this->mode) {
            case "input":
                return $this->makeInput($value, $attrs);
                break;
            case "confirm":
                return $this->makeConfirm($value, $attrs);
                break;
        }
    }

    public function getElementName()
    {
        if ($this->elementName === null) {
            if ($this->getScope()) {
                $this->elementName = $this->getScope() . '[' . $this->get('name') . ']';
            } else {
                $this->elementName = $this->get('name');
            }
        }
        return $this->elementName;
    }

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

    public function makeConfirm($value)
    {
        $name = $this->getElementName();
        return "<label class='form_label form_{$this->type}'><input type='hidden' name='{$name}' value='{$value}'>" . nl2br($value) . "</label>";
    }
}

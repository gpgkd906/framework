<?php
declare(strict_types=1);
namespace Std\FormManager\Element;

use Zend\InputFilter\InputFilter;

interface FormElementInterface
{

    /**
    * 要素の属性アクセサー、値の設定
    * @param string $name 属性名
    * @param array $value 属性値
    * @return
    */
    public function set($name, $value);

    /**
    * 要素の属性アクセサー、値の参照
    * @param string $name 属性名
    * @return
    */
    public function get($name);

    /**
    * 要素のclass追加
    * @param array $class class名
    * @return
    */
    public function addClass($class);

    /**
    * 要素のclass削除
    * @param array $class class名
    * @return
    */
    public function removeClass($class);

    /**
    * 要素値の廃棄
    * @return
    */
    public function clear();

    /**
    * バリデーションルール設定
    * @param integer $rule バリデーションチェッカールール値
    * @param string $error_message エラーメッセージ
    * @return
    */
    public function addValidator(InputFilter $InputFilter);

    /**
    * バリデーション処理
    * @return
    */
    public function isValid();

    /**
    * 要素を強制的にエラーにする
    * @param string $error_message
    * @return
    */
    public function forceError($error_message = null);

    /**
    * 一般要素のインプットモード
    * @param string/integer $value 要素値
    * @param array 要素の属性
    * @return
    */
    public function makeInput($value = null, $attr = null);

    public function makeConfirm($value);
}

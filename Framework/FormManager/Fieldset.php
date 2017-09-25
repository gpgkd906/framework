<?php
declare(strict_types=1);
namespace Framework\FormManager;

use Framework\FormManager\Element\FormElementInterface;
use Framework\Repository\Repository\EntityInterface;
use Framework\EventManager\EventTargetInterface;
use Exception;

/**
*　フォーム自動生成ライブラリ
*/
class Fieldset implements EventTargetInterface
{
    use \Framework\EventManager\EventTargetTrait;
    const TRIGGER_SUBMIT = 'submit';

    /**
    *
    * @api
    * @var mixed $elements
    * @access private
    * @link
    */
    protected $elements = [];

    /**
    *
    * @api
    * @var mixed $name
    * @access private
    * @link
    */
    protected $name = null;

    /**
    *
    * @api
    * @var mixed $data
    * @access private
    * @link
    */
    protected $data = null;

    /**
    *
    * @api
    * @var mixed $form
    * @access private
    * @link
    */
    private $form = null;

    /**
    *
    * @api
    * @var mixed $fieldset
    * @access private
    * @link
    */
    protected $fieldset = null;

    public function __construct($form, $fieldset = [])
    {
        if (isset($fieldset['name'])) {
            $this->setName($fieldset['name']);
            unset($fieldset['name']);
        }
        if (isset($fieldset['fieldset'])) {
            $fieldset = $fieldset['fieldset'];
        }
        $this->setForm($form);
        if (!empty($fieldset)) {
            $this->setFieldset($fieldset);
        }
        //データ配置
        $data = $form->getData($this->getName());
        $name = $this->getName();
        $this->setData($data);
    }

    /**
    *
    * @api
    * @param mixed $fieldset
    * @return mixed $fieldset
    * @link
    */
    public function setFieldset($fieldset)
    {
        return $this->fieldset = $fieldset;
    }

    /**
    *
    * @api
    * @return mixed $fieldset
    * @link
    */
    public function getFieldset()
    {
        if ($this->fieldset === null) {
            $this->fieldset = $this->getDefaultFieldset();
        }
        return $this->fieldset;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getDefaultFieldset()
    {
        return [];
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
    *
    * @api
    * @param mixed $elements
    * @return mixed $elements
    * @link
    */
    public function setElements($elements)
    {
        return $this->elements = $elements;
    }

    /**
    *
    * @api
    * @return mixed $elements
    * @link
    */
    public function getElements()
    {
        return $this->elements;
    }

    /**
    *
    * @api
    * @param
    * @param
    * @return
    * @link
    */
    public function addElement(FormElementInterface $element, $name = null)
    {
        $element->setScope($this->getName());
        if ($name === null) {
            $name = $element->name;
        }
        $this->elements[$name] = $element;
    }

    /**
    * 生成した要素をアクセスする
    * @param string $name 要素名
    * @return
    */
    public function __get($name)
    {
        if (isset($this->elements[$name])) {
            return $this->elements[$name];
        }
    }

    /**
    *
    * @api
    * @param mixed $data
    * @return mixed $data
    * @link
    */
    public function setData($data)
    {
        return $this->data = $data;
    }

    /**
    *
    * @api
    * @return mixed $data
    * @link
    */
    public function getData()
    {
        return $this->data;
    }

    /**
    *
    * @api
    * @param string $name
    * @return
    * @link
    */
    public function setName($name)
    {
        return $this->name = $name;
    }

    /**
    *
    * @api
    * @return mixed $name
    * @link
    */
    public function getName()
    {
        if ($this->name === null) {
            $className = explode('\\', static::class);
            $className = array_pop($className);
            $name = str_replace('Fieldset', '', $className);
            if (empty($name)) {
                $name = 'default';
            }
            $this->name = $name;
        }
        return $this->name;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function initialization()
    {
        $form = $this->getForm();
        foreach ($this->getFieldset() as $name => $field) {
            $value = isset($field['value']) ? $field['value'] : null;
            $element = $form->append($field['type'], $name, $value);
            $element->set('name', $name);
            if (isset($field['inputSpecification']) && !empty($field['inputSpecification'])) {
                $field['inputSpecification']['name'] = $name;
                $element->addValidator($form->getValidatorManager()->createInputFilter($field['inputSpecification']));
            }
            if (isset($field['attrs'])) {
                foreach ($field['attrs'] as $key => $val) {
                    $element->set($key, $val);
                }
            }
            $this->addElement($element, $name);
        }
        $this->triggerEvent(self::TRIGGER_INIT);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function onSubmit()
    {
        $this->triggerEvent(self::TRIGGER_SUBMIT);
    }
}

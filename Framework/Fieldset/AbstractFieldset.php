<?php
declare(strict_types=1);

namespace Framework\Fieldset;

use Framework\Repository\Repository\EntityInterface;
use Framework\EventManager\EventTargetInterface;
use Framework\FormManager\Fieldset;

abstract class AbstractFieldset extends Fieldset implements EventTargetInterface
{
  use \Framework\EventManager\EventTargetTrait;

  CONST TRIGGER_SUBMIT = 'submit';

  private $bindEntity = null;

  /**
  * 生成した要素をアクセスする
  * @param string $name 要素名
  */
  public function get($name) {
    if (isset($this->elements[$name])) {
      return $this->elements[$name];
    }
  }

  public function __get($name)
  {
    return $this->get($name);
  }

  public function onSubmit()
  {
    $this->triggerEvent(self::TRIGGER_SUBMIT, $this->getData());
  }
}

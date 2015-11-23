<?php

namespace Framework\Fieldset;

use Form2\Fieldset;

abstract class AbstractFieldset extends Fieldset
{    
    /**
     * 生成した要素をアクセスする
     * @param string $name 要素名
     * @return
     */
	public function get($name) {
		if(isset($this->elements[$name])) {
			return $this->elements[$name];
		}
	}

    public function __get($name)
    {
        return $this->get($name);
    }
}

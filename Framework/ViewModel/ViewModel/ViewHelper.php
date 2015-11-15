<?php

namespace Framework\ViewModel\ViewModel;

use Closure;

class ViewHelper
{
    use \Framework\Application\SingletonTrait;

    public function mapRecursive($data, Closure $Closure, Closure $levelUpClosure = null)
    {
        if(is_array($data)) {
            $this->mapApply($data, 0, $Closure, $levelUpClosure);
        }
    }

    private function mapApply($data, $level, Closure $Closure, Closure $levelUpClosure = null, $parentKey = null)
    {
        foreach($data as $key => $value) {
            if(is_array($value)) {
                if($levelUpClosure) {
                    call_user_func($levelUpClosure, $key, $level);
                }
                $this->mapApply($value, $level + 1, $Closure, $levelUpClosure, $key);
            } else {
                call_user_func($Closure, $key, $value, $level, $parentKey);
            }
        }
    }
}

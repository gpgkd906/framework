<?php

class twinkle_behavior extends model_behavior_core {
	
	/**
	 * 自動フォーム生成用アダプタ
	 * @param string $col カラム名
	 * @return 
	 */
	public static function adapter($model, $target, $param, $data = null, $info = null) {
		$read = "read_" . $target;
		$write = "write_" . $target;
		if($data === null) {
			if(is_callable(array($model, $read))) {
				return call_user_func(array($model, $read), $param);
			} else {
				return array();
			}
		} else {
			if(is_callable(array($model, $write))) {
				return call_user_func(array($model, $write), $param, $data, $info);
			}
		}
	}
}
<?php

class api_behavior extends model_behavior_core {
	
	public static function rest_get($model, $target_col = null, $key, $array_model = false) {
		if($target_col === null) {
			$target_col = $model->get_primary_key();
		}
		$model->find($target_col, $key)->limit(1);
		if($array_mode) {
			return $model->get_as_array();
		} else {
			return $model->get();
		}
	}

	public static function rest_put($model, $target_col = null, $key, $data, $array_model = false) {
		if($target_col === null) {
			$target_col = $model->get_primary_key();
		}
		$model->find($target_col, $key)->limit(1);
		if(!$record = $model->get()) {
			return false;
		}
		$record->assign($data);
		$record->save();
		if($array_model) {
			return $record->to_array();
		} else {
			return $record;
		}
	}

	public static function rest_post($model, $data, $array_model = false) {
		$record = $model->new_record();
		$record->assign($data);
		$record->save();
		if($array_model) {
			return $record->to_array();
		} else {
			return $record;
		}
	}
	
	public static function rest_delete($model, $target_col = null, $key) {
		if($target_col === null) {
			$target_col = $model->get_primary_key();
		}
		return $model->find($target_col, $key)->delete();
	}
}
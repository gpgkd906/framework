<?php

class default_behavior extends model_behavior_core {

	/**
	 * 大量結果を単純に扱う
	 * 例えば数万件のデータをアクティブレコードにし、全部維持すればメモリがすぐパンクするから、コールバックで一件づつ処理していけば,大したメモリ消費は起こらない
	 */
	public static function each($model, $call) {
		$model->select();
		while($record = $model->fetch()){
			//ループの中では中断できません、互換性...
			call_user_func($call, $record);
		}
	}
	
	public static function each_array($model, $call) {
		$model->select();
		while($row = $model->fetch_as_array()){
			//ループの中では中断できません、互換性...
			call_user_func($call, $row);
		}
	}

  /**
   * active_recordの自動フィルター機能を利用してレコードをDBに挿入する
   * 生のSQLよりは遅いけど、データの体裁を自動的に整ってくれる
   */
	public static function create_record($model, $data) {
		$record = $model->new_record();
		$record->assign($data);
		$record->save();
		return $record;
	}

  /**
   * レコードが存在すれば更新する、存在しなければ追加する
   */
	public static function write_record($model, $data) {
		$record = false;
		$pri_key = $model->get_primary_key();
		if(isset($data[$pri_key][0])) {
			$record = $model->find($pri_key, $data[$pri_key])->get();
		}
		if(!$record) {
			$record = $model->new_record();
		}
		$record->assign($data);
		$record->save();
		return $record;
	}

	/**
	 * 検索条件を一気に設定
	 * @param array 
	 * @return
	 */
	public static function search($model,Array $condition){
		foreach($condition as $key=>$cond){
			$model->find($key, $cond);
		}
	}
	
	/**
	 * 持続的な検索条件を一気に設定 
	 * @param array 
	 * @return
	 */
	public static function condition($model,Array $condition){
		foreach($condition as $key=>$cond){
			$model->add_filter($key, $key, $cond);
		}
	}
	
	/**
	 * 複数のレコードを一気に更新する
	 * @return
	 */
	public static function update_all($model, $col, $val) {
		return $model->set($col, $val)->update();
	}
	
	/**
	 * 更新情報を一気に設定する
	 * @param array 
	 * @return $this
	 */
	public static function put($model,Array $data){
		foreach($data as $key=>$item){
			$model->set($key, $item);
		}
		return $model;
	}
	
	/**
	 * 最大値を数える
	 * @param mix $col カラム名
	 * @return 
	 */
	public static function max($model, $col = null) {
		if($col !== null && $model->is_column($col)) {
			$cnt = $model->select("max(" . $model->quote($col) . ") as col")->fetch_as_array();
			return $cnt["col"];
		}
	}
  
	/**
	 * 最小値を数える
	 * @param mix $col カラム名
	 * @return 
	 */
	public static function min($model, $col = null) {
		if($col !== null && $model->is_column($col)) {
			$cnt = $model->select("min(" . $model->quote($col) . ") as col")->fetch_as_array();
			return $cnt["col"];
		}
	}
  
	/**
	 * 合計値を数える
	 * @param mix $col カラム名
	 * @return 
	 */
	public static function sum($model, $col = null) {
		if($col !== null && $model->is_column($col)) {
			$cnt = $model->select("sum(" . $model->quote($col) . ") as col")->fetch_as_array();
			return $cnt["col"];
		}    
	}

	/**
	 * 指定するカラムの全て異なる値を取得する
	 * @param string $col カラム名
	 * @return 
	 */
	public static function distinct($model, $col = null) {
		if($col !== null && $model->is_column($col)) {
			$pri_key = $model->get_primary_key();
			$model->skip_relation();
			$tmp = $model->select("DISTINCT " . $model->quote($col) . "," . $model->quote($pri_key))->fetchall_as_array();
			$res = array_column($tmp, $col, $pri_key);
			/* $res = array(); */
			/* foreach($tmp as $row) { */
			/* 	$res[$row[$pri_key]] = $row[$col]; */
			/* } */
			return $res;
		}    
	}

	/**
	 * 古い順で指定件数を取得
	 * @param integer/string $num 指定件数
	 * @return
	 */
	public static function bottom($model, $num = 5) {
		$col = $model->get_primary_key();
		$from = $model->get_from();
		$num = is_array($num) ? join(",", $num) : $num;
		return $model->order("{$from}.`{$col}` asc")->limit($num)->getall();
	}
	
	/**
	 * 新着順で指定件数を取得
	 * @param integer/string $num 指定件数
	 * @return
	 */
	public static function top($model, $num = 5) {
		$col = $model->get_primary_key();
		$from = $model->get_from();
		$num = is_array($num) ? join(",", $num) : $num;
		return $model->order("{$from}.`{$col}` desc")->limit($num)->getall();	  
	}
	
	
	/**
	 * 最初の一件
	 * @return
	 */
	public static function first($model) {
		$res = $model->bottom(1);
		return $res[0];
	}
	
	
	/**
	 * 最後の一件
	 * @return
	 */
	public static function last($model) {
		$res = $model->top(1);
		return $res[0];
	}
	
	/**
	 * 主キーで配列情報を取得
	 * @return
	 */
	public static function find_one($model, $primary) {
		return $model->find($model->get_primary_key(), $primary)->limit(1)->get_as_array();
	}
	
	/**
	 * idで更新する
	 * @return
	 */
	public static function upgrade_by_id($model, $id, $new_record) {
		$old_record = $model->find_by_id($id);
		$old_record->assign($new_record);
		$old_record->save();
	}
}
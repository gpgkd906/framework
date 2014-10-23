<?php
/**
 * active_record_core
 * 
 * ActiveRecord クラス
 *
 * @author 2014 Chen Han 
 * @package framework.core
 * @link 
 */
namespace Core\Model;
use Core\Model\ModelCore;

class EntityCore {
    /**
	 * テーブル名
	 * @api
	 * @var String
	 * @link
	 */
	protected static $from;
    /**
	 * テーブルカラムにあるデータセット
	 * @api
	 * @var Array
	 * @link
	 */
	private $store = array();
    /**
	 * テーブルカラムにないデータセット
	 * @api
	 * @var Array
	 * @link
	 */
	private $extend = array();
    /**
	 * プライマリキー
	 * @api
	 * @var String
	 * @link
	 */
	protected static $primary_key;
    /**
	 * プライマリキーの値
	 * @api
	 * @var Mixed
	 * @link
	 */
	private $primary_value = null;
    /**
	 * モデルのカラムの反転配列。
	 * @api
	 * @var Array
	 * @link
	 */
	protected static $store_schema;

    /**
	 * レコードが実際に変更されたかどか
	 * @api
	 * @var 
	 * @link
	 */
	private $real_changed = false;

    /**
	 * 遅延静的束縛：現在のActiveRecordのカラムにあるかどか
	 * @api
	 * @param String $col カラム名
	 * @return
	 * @link
	 */
	public static function has_column($col) {
		return isset(self::$store_schema[$col]);
	}
    /**
	 * 遅延静的束縛：ActiveRecordのテーブル名を取得
	 * @api
	 * @return
	 * @link
	 */
	public static function get_from() {
		return self::$from;
	}
    /**
	 * 遅延静的束縛：ActiveRecordのプライマリーキーを取得
	 * @api
	 * @return
	 * @link
	 */
	public static function get_primary_key() {
		return self::$primary_key;
	}

    /**
	 * 対応するORMインスタンスのメソッドを呼び出す
	 * @api
	 * @param String $name メソッド名  
	 * @param Mixed $param メソッドパラメタ
	 * @return
	 * @link
	 */
	public function __call($name, $param) {
		return call_user_func_array(array(ModelCore::select_model(static::get_from()), $name), $param);
	}
	
    /**
	 * ActiveRecord構造関数
	 * @api
	 * @param Array $row パッケージするデータセット  
	 * @return
	 * @link
	 */
	public function __construct($row){
		$this->store = $row;
		$primary_key = static::get_primary_key();
		if(isset($this->store[$primary_key])) {
			$this->primary_value = $this->store[$primary_key];
		}
	}

	
    /**
	 * データセットから指定キーで値を取得
	 * @api
	 * @param String $name データキー
	 * @return
	 * @link
	 */
	public function __get($name){
		return isset($this->store[$name]) ? $this->store[$name] : (isset($this->extend[$name]) ? $this->extend[$name] : null);
	}
	
    /**
	 * データセットから指定キーで値が存在するかどかのチェック
	 * @api
	 * @param String $name データキー
	 * @return
	 * @link
	 */
	public function __isset($name) {
		return isset($this->store[$name]) ? true : (isset($this->extend[$name]) ? true : false);
	}

    /**
	 * データセットにキーを指定して更新する
	 * @api
	 * @param String $name データキー
	 * @param Mixed $value 値
	 * @return
	 * @link
	 */
	public function __set($name, $value){
		if(!static::has_column($name)) {
			return $this->extend[$name] = $value;
		}
		if($this->store[$name] !== $value) {
			$this->real_changed = true;
		}
		return $this->store[$name] = $value;
	}
	
    /**
	 * 主キー値取得
	 * @api
	 * @return
	 * @link
	 */
	public function get_primary_value(){
		return $this->primary_value;
	}
	
    /**
	 * 指定する値取得
	 * @api
	 * @param Mixed $cols カラム名(複数可)
	 * @return
	 * @link
	 */
	public function get_value($cols) {
		if(is_string($cols)) {
			$cols = explode(",", $cols);
		}
		$vals = array();
		foreach($cols as $col) {
			$vals[] = $this->store[$col];
		}
		return join(",", $vals);
	}

	/**
	 * データ一括更新
	 * @api
	 * @param Array $data 更新するデータ
	 * @return
	 * @link
	 */
	public function assign($data) {
		foreach($data as $name => $value) {
			$this->__set($name, $value);
		}
	}
	
	/**
	 * データバインド
	 * bind array <=> assign hash
	 * 
	 * @api
	 * @param Array $arr データ  
	 * @param Array? $type データ型
	 * @return
	 * @link
	 */
	public function bind_array($arr, $type = null) {
		if($type === null) {
			$type = array_keys($this->store);
		}
		$data = array_combine($type, $arr);
		$this->assign($data);
	}

	/**
	 * タイプ取得
	 * 
	 * @api
	 * @return
	 * @link
	 */
	public function get_type() {
		return array_keys($this->store);
	}

	/**
	 * データセットを配列で取得
	 * @api
	 * @return
	 * @link
	 */
	public function to_array() {
		return array_merge($this->store, $this->extend);
	}
	
	/**
	 * データセットの変更をデータベースに反映
	 *
	 *値が実質的に変更されてない場合はSQLの生成及び発行を飛ばす：「最も早いSQLはSQLを発行しないこと」
	 *
	 *更新時の挙動は要検討、論理上プライマリキーは変更すべきものではない
	 * 
	 *実際業務では変更される場合もあるかもしれない;
	 * @api
	 * @return
	 * @link
	 */
	public function save(){
		if(empty($this->store)) {
			trigger_error("invalid record, invalid store", E_USER_NOTICE);
			return false;
		}
		$value = null;
		$primary_key = static::get_primary_key();
		if(isset($this->primary_value)) {
			if(!$this->real_changed) {
				return false;
			}
			if(static::has_column("update_dt")) {
				$this->store["update_dt"] = $_SERVER["REQUEST_TIME"];				
			}
			$this->skip_filter();
			$this->find($primary_key, $this->primary_value)->put($this->store)->update(true);
			$this->real_changed = false;
			$this->primary_value = $this->get_value($primary_key);
			$value = $this->primary_value;
		} else {
			if(static::has_column("register_dt")) {
				$this->store["register_dt"] = $this->store["update_dt"] = $_SERVER["REQUEST_TIME"];
			}
			$this->put($this->store)->insert();
			$value = $this->last_id();
			$this->primary_value = $value;
			$this->store = array_merge(array($primary_key => $this->primary_value), $this->store);
		}
		return $value;
	}
	
	/**
	 * データ削除
	 * 
	 * @api
	 * @return
	 * @link
	 */
	public function delete() {
		if(isset($this->primary_value)) {
			$this->store = array();
			return $this->find(static::get_primary_key(), $this->primary_value)->delete();
		}
	}
}

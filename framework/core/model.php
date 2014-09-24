<?php

/**
 * model_core.php
 *
 * myFramework : Origin Framework by Chen Han https://github.com/gpgkd906/framework
 *
 * Copyright 2014 Chen Han
 *
 * Licensed under The MIT License
 *
 * @copyright Copyright 2014 Chen Han
 * @link
 * @since
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * model_core
 * モデルスーパークラス
 *
 * @author 2014 Chen Han 
 * @package framework.core
 * @link 
 */
class model_core extends model_driver {

    /**
	 * プライマリキー
	 * @var array 
	 * @link http://
	 */
	public $primary_keys = array();
	/**
	 * 対応するActiveRecordクラス名
	 * @api
	 * @var String
	 * @link
	 */
	public $active_record_name = "active_record_core";
    /**
	 * モデルのインスタンスキャッシュ
	 * @var array 
	 * @link http://
	 */
	public static $select_from = array();

    /**
	 * データベース接続情報
	 * @var array 
	 * @link http://
	 */
	public static $DSN = null;

    /**
	 * モデルのクラスファイルパース
	 * @var string 
	 * @link http://
	 */
	public static $model_path = null;

    /**
	 * 結合情報
	 * @var array 
	 * @link http://
	 */
	public $relation = array();

    /**
	 * オンメモリーのキャッシュ機構
	 * @var array
	 * @link http://
	 */
	protected $cache = array();
  
    /**
	 * ビヘイビア設定機構
	 * @var array
	 * @link http://
	 */
	public $acts_as = array(); 

    /**
	 * ビヘイビア機構
	 * @var array
	 * @link http://
	 */
	protected $behaviors_pool = array();

    /**
	 * ビヘイビア機構(共通)
	 * @var array
	 * @link http://
	 */
	static private $behaviors = array();
    /**
	 * ビヘイビア設定パス
	 * @var array
	 * @link http://
	 */
	static private $behaviors_path = "";

    /**
	 * 構造器、構造時に自動的に結合関係を適用する
	 * @api
	 * @param array $DSN  
	 * @return
	 */
	public function __construct($DSN) {
		parent::__construct($DSN);
		if(!empty($this->relation)) {
			foreach($this->relation as $key => $relate) {
				$relate[0] = self::select_model($relate[0]);
				array_unshift($relate, $key);
				call_user_func_array(array($this, "add_relation"), $relate);
			}
		}
		$this->load_behavior("default");
		if(!empty($this->acts_as)) {
			foreach($this->acts_as as $behavior) {
				$this->load_behavior($behavior);
			}
		}
	}

    /**
	 * 定義した結合情報を取得する
	 * @api
	 * @return array
	 */
	public function get_defined_relation() {
		return $this->relation;
	}


    /**
	 * モデルのインスタンスを生成する
	 * @api
	 * @param String $from テーブル名 
	 * @param String $path モデルファイルパース 
	 * @param Array $DSN 接続情報
	 * @return Object モデルのインスタンス
	 */
	public static function select_model($from, $path = null, $DSN = null) {
		$DSN = isset($DSN) ? $DSN : self::$DSN;
		$path = isset($path) ? $path : self::$model_path;
		//feed back
		self::$DSN = $DSN;
		self::$model_path = $path;
		if(empty($from)) {
			$from = "__empty_table_name__";
		}
		if(!isset(self::$select_from[$from])) {
			$from_path = $path . $from . ".model.php";
			if(is_file($from_path)) {
				$model_name = $from . "_model";
				require_once $from_path;
				$model = new $model_name($DSN);
			} else {
				$model = new model_core($DSN);
			}
			$model->from($from);
			self::$select_from[$from] = $model;
		}
		return self::$select_from[$from];
	}
  
    /**
	 * モデルクラスのデータソース情報(データベーステーブル名)設定
	 * @api
	 * @param String $table データベーステーブル名
	 * @param Boolean $reset deprecated
	 * @return
	 * @link
	 */
	public function from($table,$reset=true){
		parent::from($table, $reset);
		self::$select_from[$table] = $this;
		return $this;
	}

	/**
	 * 結果セットから一件だけデータを読み出す,active_record風でラップして返る
	 * @api 
	 * @return ActiveRecord $record
	 * @link
	 */
	public function fetch(){
		$row = parent::fetch();
		if($row) {
			$active_record_name = $this->active_record_name;
			$row = new $active_record_name ($row);
		}
		return $row;
	}

	/**
	 * 結果セットから全てデータを読み出す,active_record風でラップして返る
	 * @api 
	 * @return Array $records
	 * @link
	 */
	public function fetchall() {
		$data = parent::fetchall();
		$active_record_name = $this->active_record_name;
		foreach($data as $key => $row) {
			$data[$key] = new $active_record_name($row);
		}
		return $data;
	}

	/**
	 * 結果セットから一件だけデータを読み出す,配列として返る
	 * @api
	 * @return Array $row
	 * @link
	 */
	public function fetch_as_array() {
		$row =  parent::fetch();
		return $row;
	}

	/**
	 * 結果セットから全てデータを読み出す,配列として返る
	 * @api
	 * @return Array $rows
	 * @link
	 */
	public function fetchall_as_array() {
		$res = parent::fetchall();
		return $res;
	}

	/**
	 * SELECT SQLを構成し、クエリーする
	 *
	 * 取得するデータのカラムをパラメタで渡すことで絞ることができる
	 * @api
	 * @param... String $cols 
	 * @return Array $row
	 * @link
	 */
	public function select() {
		$args = func_get_args();
		return call_user_func_array("parent::select", $args);
	}

	/**
	 * DELETE SQLを構成し、クエリーする
	 *
	 * @api
	 * @param Array? $args 追加設定削除用パラメタ
	 * @link
	 */
	public function delete($args = null) {
		parent::delete($args);
	}

	/**
	 * Insert SQLを構成し、クエリーする
	 *
	 * @api
	 * @link
	 */
	public function insert() {
		parent::insert();
	}
  
	/**
	 * Update SQLを構成し、クエリーする
	 *
	 * @api
	 * @param Boolean $active_record ActiveRecordからの更新であるかどか 
	 * @link
	 */
	public function update($active_record = false) {
		parent::update($active_record);
	}

    /**
	 * プライマリーキーを取得する
	 * @api
	 * @return String $primary_key プライマリーキー
	 * @link
	 */
	public function get_primary_key(){
		return $this->primary_keys[$this->from];
	}

    /**
	 * 廃止
	 * @deprecated
	 */
	public function get_primary_set(){
		return $this->primary_sets[$this->from];
	}

	/**
	 * active_record風の新しいレコードをメモリ上生成する(saveするまでデータベースに反映しない)
	 * @api
	 * @return ActiveRecord $record 新しいレコード
	 * @link
	 */
	public function new_record(){
		$record = array_fill_keys($this->columns(), "");
		$key = $this->get_primary_key();
		unset($record[$key]);
		$active_record_name = $this->active_record_name;
		$record = new $active_record_name($record);
		return $record;
	}

	/**
	 * データをその場で新規ActiveRecordを生成してデータベースに反映する
	 * @api
	 * @param Array $data 保存したいデータ
	 * @return ActiveRecord $record 保存したレコード
	 * @link
	 */
	public function create_record($data){
		$record = $this->new_record();
		$record->assign($data);
		$record->save();
		return $record;
	}
  
    /**
	 * モデルのカラムであるかどか
	 * @param String $col カラム
	 * @return Boolean
	 */
	public function is_column($col) {
		$this->columns();
		return in_array($col, $this->columns); 
	}


    /**
	 * データベースをクリアする
	 * @api
	 * @return
	 * @link
	 */
	public function truncate() {
		$this->query("TRUNCATE table " . $this->get_from());
	}

    /**
	 * select * from {table} [where ...] limit 1;
	 * アクティブレコード式
	 * @api
	 * @return
	 * @link
	 */
	public function get(){
		return $this->limit(1)->select()->fetch();
	}
  

    /**
	 * select * from {table} [where ...] limit 1;
	 * 配列式
	 * @api
	 * @return
	 * @link
	 */
	public function get_as_array() {
		return $this->limit(1)->select()->fetch_as_array();
	}
  

    /**
	 * select * from {table} [where ...];
	 * アクティブレコード式
	 * @api
	 * @return
	 * @link
	 */
	public function getAll(){
		return $this->select()->fetchall();
	}
  

    /**
	 * select * from {table} [where ...];
	 * 配列式
	 * @api
	 * @return 
	 * @link
	 */
	public function getAll_as_array(){
		return $this->select()->fetchall_as_array();
	}
  
    /**
	 * DISTINCT条件を設定する
	 * @api
	 * @return
	 * @link
	 */
	protected function check_distinct() {
		$distinct = false;
		if(strpos($this->group, "GROUP BY") !== false) {
			$distinct = str_replace("GROUP BY", "", $this->group);
			$this->group = null;
		}
		return $distinct;
	}


    /**
	 * 件数を数える
	 * @api
	 * @param Mixed $col カラム名
	 * @return Integer 数えた結果
	 * @link
	 */
	public function count($col = null){
		$distinct = $this->check_distinct();
		if($distinct) {
			$select = "COUNT(DISTINCT{$distinct}) as cnt";
		} else {
			$select = ($col !== null && $this->is_column($col)) ? "COUNT(" . $this->quote($col) . ") as cnt" : "COUNT(*) as cnt";
		}
		$cnt = $this->select($select)->fetch_as_array();
		return $cnt["cnt"];
	}

	/**
	 * serializedしたものかどかのチェック
	 * 
	 * wordpressから借りたもの、wordpressありがとう
	 * @api
	 * @param Mixed $data 
	 * @param Boolean $strict
	 * @return Boolean
	 * @link http://codex.wordpress.org/Function_Reference/is_serialized
	 */
	public static function is_serialized($data, $strict = true) {
		// if it isn't a string, it isn't serialized
		if ( ! is_string( $data ) )
			return false;
		$data = trim( $data );
		if ( 'N;' === $data )
			return true;
		$length = strlen( $data );
		if ( $length < 4 )
			return false;
		if ( ':' !== $data[1] )
			return false;
		if ( $strict ) {
			$lastc = $data[ $length - 1 ];
			if ( ';' !== $lastc && '}' !== $lastc )
				return false;
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace )
				return false;
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 )
				return false;
			if ( false !== $brace && $brace < 4 )
				return false;
		}
		$token = $data[0];
		switch ( $token ) {
			case 's' :
				if ( $strict ) {
					if ( '"' !== $data[ $length - 2 ] )
						return false;
				} elseif ( false === strpos( $data, '"' ) ) {
					return false;
				}
				// or else fall through
			case 'a' :
			case 'O' :
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b' :
			case 'i' :
			case 'd' :
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
		}
		return false;
	}	
  
  

    /**
	 * rails風アクション群
	 *
	 * データベースのカラムによって各種メソッドを自動生成
	 *
	 * 他のメソッドと連携とることも可能です
	 *
	 *###テーブルにnameカラム, ageカラムがある場合
	 *
	 *     $model->find_by_name("***"); //select * from table where name = "***" limit 1;
	 *     $model->find_all_by_name("***"); //select * from table where name = "***";
	 *     $model->update_by_name("***", array("age" => 20)); //update table set age=20 where name = "***";
	 *     $model->write_by_name("***", array("age" => 20)); //update table set age=20 where name = "***";
	 *     $model->count_by_name("***"); //select count(*) from table group by name;
	 *     $model->count_value_by_name("***"); //select count(primary_id), name from table group by name;
	 *     $model->exists_by_name("***"); //select * from {table} where name = "***" limit 1;
	 *     $model->upgrade_by_name("***", array("age" => "20")); //update table set age=20 where name = "***";
	 *                                                           //あるいは insert into table (name, age) values ("***", "20");
	 *
	 *###他のメソッドと連携をとる場合
	 *    
	 *     $model->find("age", 20, ">");
	 *     $model->find_by_name("***"); // select * from table where name = "***" and age > 20 limit 1;
	 *
	 * @param string $name アクション名
	 * @param mix $param アクションパラメタ
	 * @return
	 */
	public function __call($name, $param){
		
		if(isset($param[0])) {
			if(strpos($name, "find_by_") === 0) {
				$name = str_replace("find_by_", "", $name);
				$this->find($name, $param[0])->limit(1);
				if(isset($param[1]) && $param[1] === true) {
					return $this->get_as_array();
				} else {
					return $this->get();
				}
			} elseif(strpos($name, "find_all_by_") === 0) {
				$name = str_replace("find_all_by_", "", $name);
				if(empty($param[0])) {
					return array();
				}
				$this->find($name, $param[0]);
				if(isset($param[1]) && $param[1] === true) {
					return $this->getall_as_array();
				} else {
					return $this->getall();
				}
			} elseif(strpos($name, "update_by_") === 0) {
				return $this->_update_by($name, $param);
			} elseif(strpos($name, "write_by_") === 0) {
				return $this->_upgrade_by($name, $param);
			} elseif(strpos($name, "count_by_") === 0) {
				return $this->_count_by($name, $param);
			} elseif(strpos($name, "count_value_by_") === 0) {
				return $this->_count_by_value($name, $param);
			} elseif(strpos($name, "exists_by_") === 0) {
				return $this->_exists_by($name, $param);
			} elseif(strpos($name, "upgrade_by_") === 0) {
				return $this->_upgrade_by($name, $param);
			}
		}
		return $this->behavior_call($name, $param);
	}

    /**
	 * 条件に一致するレコードを全更新する
	 * @api
	 * @param String $name 
	 * @param Mixed $param 
	 * @return
	 */
	private function _update_by($name, $param) {
		$name = str_replace("update_by_", "", $name);
		return $this->find($name, $param[0])->put($param[1])->update();
	}

    /**
	 * 同じカラムの違う値を数える
	 * @api
	 * @param String $name 
	 * @param Mixed $param 
	 * @return
	 */
	private function _count_by($name, $param) {
		$name = str_replace("count_by_", "", $name);
		return $this->find($name, $param[0])->group($this->quote($name))->count($name);
	}

    /**
	 * 同じカラムの指定した値のそれぞれの数を数える
	 * @api
	 * @param String $name 
	 * @param Mixed $param 
	 * @return
	 */
	private function _count_by_value($name, $param) {
		$val = isset($param[0]) ? $param[0] : null;
		$name = str_replace("count_value_by_", "", $name);
		$tmp = $this->find($name, $val)->group($this->quote($name))->select("count(" . $this->get_primary_key() . ") as cnt", $this->quote($name))->fetchall_as_array();
		if(is_array($val)) {
			$res = array_column($tmp, "cnt", $name);
			foreach($val as $v) {
				if(!isset($res[$v])) {
					$res[$v] = 0;
				}
			}
		} else {
			$res = $tmp[0]["cnt"];
		}
		return $res;
	}

    /**
	 * カラム指定してレコード存在するかどかをチェック
	 * @api
	 * @param String $name 
	 * @param Mixed $param 
	 * @return
	 */
	private function _exists_by($name, $param) {
		$name = str_replace("exists_by_", "", $name);
		return $this->find($name, $param[0])->group($this->quote($name))->select($this->quote($name))->fetchall_as_array();
	}

    /**
	 * カラム指定してレコードを更新する
	 * @api
	 * @param String $name 
	 * @param Mixed $param 
	 * @return
	 */
	private function _upgrade_by($name, $param) {
		$val = $param[0];
		$name = str_replace("upgrade_by_", "", $name);
		$data = $param[1];
		$record = $this->find($name, $val)->limit(1)->get();
		if(!$record){
			$record = $this->new_record();
			$data[$name] = $val;
		}
		$record->assign($data);
		$record->save();
		return $record->to_array();
	}

    /**
	 * カラム名を`テーブル`.`カラム`形式で取得する
	 *
	 *
	 *###テーブルtestにnameカラム, ageカラムがある場合
	 *
	 *     echo $model->name; //`table`.`name`
	 *     echo $model->age; //`table`.`age`
	 *
	 * @api
	 * @param String $name カラム名
	 * @return
	 */
	public function __get($name){
		if(in_array($name, $this->columns())) {
			return $this->get_from() . ".`" . $name . "`";
		}
	}
  
    /**
	 * オンメモリーのキャッシュ機構読み込み処理
	 *
	 * memcachedなどを使う場合はデータをキャッシュすることでパフォーマンス改善が図れる
	 * @param string $key キャッシュキー
	 * @return Mixed
	 * @link http://
	 */
	protected function get_cache($key) {
		/*
		//memcachedをここで使いたいかもしれません、出来ればhandlersocketを検討したいですが、handlersocketが使えない場合はmemcachedもわるくはない。
		return memcached->get($key);
		*/
		if(isset($this->cache[$key])) {
			return $this->cache[$key];
		}
	}

    /**
	 * オンメモリーのキャッシュ機構書き込み処理
	 * @param string $key キャッシュキー
	 * @param mix $value キャッシュ値
	 * @param number $expire キャッシュ寿命(memcachedを利用する際だけ、有効になる)
	 * @link http://
	 */
	protected function set_cache($key, $value, $expire = 60) {
		/*
		//memcachedをここで使いたいかもしれません、出来ればhandlersocketを検討したいですが、handlersocketが使えない場合はmemcachedもわるくはない。
		memcached->set($key, $value, $expire);
		*/
		$this->cache[$key] = $value;
	}


    /**
	 * ビヘイビアをロードする
	 * @param string $behavior
	 * @return
	 */
	public function load_behavior($behavior) {
		if(($behavior = self::import_behavior($behavior)) && !in_array($behavior, $this->behaviors_pool)) {
			array_unshift($this->behaviors_pool, $behavior);
		}
		return $this;
	}

    /**
	 * ビヘイビアをアンロードする
	 * @param string $behavior
	 * @return
	 */
	public function unload_behavior() {
		$unloads = func_get_args();
		$tmp = array();
		foreach($unloads as $unload) {
			$tmp[] = self::import_behavior($unload);
		}
		$this->behaviors_pool = array_diff($this->behaviors_pool, $tmp);
		return $this;
	}

	/**
	 * 設定したビヘイビアを呼び出す
	 * @api
	 * @param String $name メソッド名  
	 * @param Mixed $param メソッドに渡すパラメタ   
	 * @return
	 * @link
	 */
	final public function behavior_call($name, $param = array()) {
		array_unshift($param, $this);
		foreach($this->behaviors_pool as $behavior) {
			if(method_exists($behavior, $name) && is_callable(array($behavior, $name))) {
				return call_user_func_array(array($behavior, $name), $param);
			}
		}
	}
  
	/**
	 * ビヘイビアパスを設定
	 * @api
	 * @param String $behavior_path ビヘイビアパス
	 * @return
	 * @link
	 */
	static public function behavior_path($behavior_path) {
		self::$behaviors_path = $behavior_path;
	}

	/**
	 * ビヘイビアを読み込む
	 * @api
	 * @param String $behavior ビヘイビア名
	 * @return
	 * @link
	 */
	static private function import_behavior($behavior) {
		if(isset(self::$behaviors[$behavior])) {
			return self::$behaviors[$behavior];
		}
		$behavior_file = self::$behaviors_path . $behavior . ".behavior.php";
		if(is_file($behavior_file)) {
			require_once $behavior_file;
			return self::$behaviors[$behavior] = $behavior . "_behavior";
		} else {
			return self::$behaviors[$behavior] = false;
		}
	}
  
    /**
	 * 最後にクエリーしたSQL分を表示する
	 * @return
	 */
	public function log() {
		echo "<pre>";
		print_r($this->get_last_query());
		echo "</pre>";
	}
}

/**
 * active_record_core
 * 
 * ActiveRecord クラス
 *
 * @author 2014 Chen Han 
 * @package framework.core
 * @link 
 */
class active_record_core {
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
		return call_user_func_array(array(model_core::select_model(static::get_from()), $name), $param);
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
			$vals = $this->store[$col];
		}
		return $vals;
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

/**
 * model_behavior_core
 * 
 *
 * @author 2014 Chen Han 
 * @package framework.core
 * @link 
 */
class model_behavior_core {
	
}

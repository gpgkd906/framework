<?php
namespace Framework\Core\Model\SqlBuilder;
use Framework\Core\Interfaces\Model\SchemaInterface;

class MySqlBuilder {

    private $Schema = null;
    private $sql = null;
	private $args = array();
    
	protected $set = array();
	protected $set_args = array();
	protected $find = array();
	protected $filter = array();
	protected $join = array();
	protected $from = null;
	protected $table = null;
	protected $where = array();
	protected $where_condition = array();
	protected $where_filter = array();
	protected $order= null;
	protected $group = null;
	protected $limit=array();
	protected $columns = array();
	protected $relate_columns = array();
	protected $join_columns = array();
	protected $skip_filter = array();
	protected $relate = array();
	protected $skip_relate = array();
	protected $last_skip_relate = array();
	protected $alias = array();

    public function setSchema(SchemaInterface $Schema)
    {
        $this->Schema = $Schema;
        $this->setTable($Schema->getName());
    }

	private function setTable($table){
		$this->table = $table;
		$this->from = '`' . $table . '`';
	}

/**
 * 持続的な検索条件を設定する
 * @param string $key フィルタ名
 * @param max 　　$where 検索カラム名(集)
 * @param mix 　　$bind  検索値(集)
 * @param string $opera 検索方法
 * @return void
 */
	public function add_filter($key, $where, $bind = null, $opera = "=") {
		$this->filter[$key] = array($where, $bind, $opera);
	}

/**
 * 設定した持続的な検索条件を削除する
 * @param string $key フィルタ名
 * @return void
 */
	public function remove_filter($key) {
		if(isset($this->filter[$key])) {
			unset($this->filter[$key]);
		}
	}
  

/**
 * 設定した全ての持続的な検索条件を削除する
 * @return void
 */
	public function clear_filter() {
		$this->filter = array();
	}


/**
 * 設定した持続的な検索条件をスキップするように、スキップ情報を設定
 * @param string $key フィルタ名
 * @return void
 */
	public function skip_filter($key = "all") {
		if(empty($key)) {
			return false;
		}
		if($key === "all") {
			$this->skip_filter = $this->filter;
		} else {
			$this->skip_filter[$key] = true;
		}
	}

/**
 * 設定したフィルタを取得
 * @param string $key フィルタ名
 * @return array フィルタ情報
 */
	public function get_filter($key) {
		return $this->filter[$key];
	}

  
/**
 * 設定したフィルタをスキップ済みして取得する
 * @return array フィルタ情報
 */
	public function get_all_filter() {
		$filter = array_diff_key($this->filter, $this->skip_filter);
		$this->skip_filter = array();
		return $filter;
	}
  
/**
 * カラム名の別名を設定
 * @param string $col カラム名
 * @param string $alias 別名
 * @return
 */
	public function alias($col, $alias) {
		$this->alias[$col] = $alias;
	}
  
	//relate (long live join)

/**
 * 結合情報を追加する
 * @api 
 * @return
 * @link
 */
	public function add_relation() {
		$args = func_get_args();
		$key = array_shift($args);
		$this->relate[$key] = $args;
	}

/**
 * 設定した結合情報を削除する
 * @param string $key $結合情報名
 * @return void
 */
	public function remove_relation($key) {
		if(isset($this->relate[$key])) {
			unset($this->relate[$key]);
		}
	}

/**
 * 設定した結合情報を全て削除する
 * @api
 * @return
 */
	public function clear_relation() {
		$this->relate = array();
	}

/**
 * 結合のスキップ情報を設定する
 * @api
 * @param string $key 結合情報名
 * @return void
 */
	public function skip_relation($key = "all") {
		if($key === "all") {
			$this->skip_relate = $this->relate;
		} else {
			$this->skip_relate[$key] = true;
		}
	}


/**
 * 最後の結合のスキップ情報を取得
 * @return array 
 */
	public function get_last_skipped_relation() {
		return $this->last_skip_relate;
	}


/**
 * 設定した結合情報で実結合処理
 * @return void
 */
	protected function check_relation() {
		$relate = array_diff_key($this->relate, $this->skip_relate);
		$this->last_skip_relate = $this->skip_relate;
		$this->skip_relate = array();
		foreach($relate as $set) {
			call_user_func_array(array($this, "join"), $set);
		}
	}
    
/**
 * モデルのテーブル名を取得
 * @api
 * @return string 
 */
	public function getTable($quoted = false){
        if($quoted) {
            return $this->table;
        } else {
            return $this->from;
        }
	}

/**
 * 検索条件を設定する
 * @api
 * @param max 　　$where 検索カラム名(集)
 * @param mix 　　$bind  検索値(集)
 * @param string $opera 検索方法
 * @return $this
 */
	public function find($where, $bind = null, $opera = "="){
		$this->find[]=array($where, $bind, $opera);
		return $this;
	}


/**
 * 設定した検索条件を取得し、全ての検索条件を解除する
 * @api
 * @return array
 */
	public function fetch_find() {
		$find = $this->find;
		$this->find = array();
		return $find;
	}
  

/**
 * 設定した検索条件をテーブルのカラム情報を照合し、情報の正当性をチェックする
 * @param array/string $where 検索キー
 * @return 照合結果、失敗する場合はfalseを返す
 */
	public function check_where($where) {
		$_check = array_reduce($where, function($set, $item) {
            if(strpos($item, ".")){
                list($_table, $_column) = explode(".", $item);
                $set[] = $_column;
            } else {
                $set[] = $item;
            }
            return $set;
        }, array());
		if(empty($this->join_columns)) {
			$this->join_columns = $this->columns();
		}
		$diff = array_diff($_check, $this->join_columns);
		return empty($diff);
	}


/**
 * NULL検索条件を設定する
 * @param string/array $where 検索カラム
 * @param boolean $null NULLかどか
 * @return
 */
	public function is_null($where, $null = true) {
		$where = $this->escape($where);
		if($null) {
			$this->where[]="(`". $where . "` IS NULL)";
		} else {
			$this->where[]="(`". $where . "` IS NOT NULL)";      
		}
	}

/**
 * 遅的にバンドする 
 * 設定した検索条件をSQL文に変換する
 * @param max 　　$where 検索カラム名(集)
 * @param mix 　　$bind  検索値(集)
 * @param string $opera 検索方法
 * @return
 */
	protected function _find($where,$bind=null,$opera = "="){
		// 0 => array(0), "" => array("") ==> not empty
		// null => array(), array() => array() ==> empty
		$bind=(array) $bind;
		if(empty($bind)) {
			return false;
		}
		$primary = false;
		if(strpos($where, ",") !== false){
			if($where === $this->get_primary_key()) {
				$primary = true;
			}
			$where = explode(",", $where);
		}else{
			$where = array($where);
		}
		if(!$this->check_where($where)) {
			trigger_error("unsafe column:[" . join(",", $where) . "] for model:{$this->table}", E_USER_WARNING);
			return false;
		}
		$arglen = count(func_get_args());
		$opera = strtolower($opera);
		if($arglen === 1){
			//do nothing
		}elseif(count($where) === 1 && count($bind) > 1){
			list($where, $bind) = $this->_find_single($where, $bind, $opera);
		}else{
			list($where, $bind) = $this->_find_multi($where, $bind, $opera);      
		}
		if($primary) {
			$this->where[] = "(" . join(" AND ", $where) . ")";		
		} else {
			$this->where[] = "(" . join(" OR ", $where) . ")";
		}
		$this->args = array_merge($this->args, $bind);
		return $this;
	}

/**
 * 遅的にバンドする 
 * 設定した検索条件をSQL文に変換するサブ処理、検索カラムと検査値は一対一という場合に働く
 * @param max 　　$where 検索カラム名(集)
 * @param mix 　　$bind  検索値(集)
 * @param string $opera 検索方法
 * @return
 */
	protected function _find_single($where, $bind, $opera) {
		if(strpos($where[0], ".") > 0) {
			$col = "`" . $this->escape(join("`.`", explode(".", str_replace("`", "", $where[0])))) . "`";
		}else {
			$from = $this->from;
			if(!in_array($where[0], $this->columns)) {
				foreach($this->relate_columns as $_from => $columns) {
					if(in_array($where[0], $columns)) {
						$from = $_from;
						break;
					}
				}
			}
			$col = $from.".`".$where[0]."`";
		}
		if($opera === "between"){
			$where[0] = $col . " between ? and ?";
		}elseif($opera === "=" || $opera === "<>" || $opera === "not"){
			$_v = array();
			foreach($bind as $dummy){
				$_v[] = "?";
			}
			if($opera === "=") {
				$where[0] = $col . " in (" . join(",", $_v) . ")";
			} else {
				$where[0] = $col . " not in (" . join(",", $_v) . ")";
			}
		} else {
			$_set = array();
			list($opera, $bind) = self::_check_like($opera, $bind);
			foreach($bind as $key => $dummy){
				$_set[] = $col . "` {$opera} ?";
			}
			$where=$_set;
		}
		return array($where, $bind);
	}
  
/**
 * 遅的にバンドする 
 * 設定した検索条件をSQL文に変換するサブ処理、検索カラムと検査値は一対多という場合に働く
 * @param max 　　$where 検索カラム名(集)
 * @param mix 　　$bind  検索値(集)
 * @param string $opera 検索方法
 * @return
 */
	protected function _find_multi($where, $bind, $opera) {
		$_b = array();
		list($opera, $bind) = self::_check_like($opera, $bind);
		foreach($bind as $key => $val){
			if(!isset($where[$key])){
				break;
			}
			$_w = $where[$key];
		  
			if(strpos($_w, ".") > 0) {
				$col = "`" . $this->escape(join("`.`", explode(".", str_replace("`", "", $_w)))) . "`";
			}else {
				$from = $this->from;
				if(!in_array($_w, $this->columns)) {
					foreach($this->relate_columns as $_from => $columns) {
						if(in_array($_w, $columns)) {
							$from = $_from;
							break;
						}
					}
				}
				$col = $from . ".`" . $_w . "`";
			}
			if(is_array($val)){
				$_v = array();
				foreach($val as $dummy){
					$_v[] = "?";
				}
				if($opera === "=") {
					$_w = $col . " in (" . join(",", $_v) . ")";
				} else {
					$_w = $col . " not in (" . join(",", $_v) . ")";
				}
				$_b = array_merge($_b, $val);
			}else{
				if(strpos($_w, "?") === false){
					$_w = $col . " {$opera} ?";
				}
				$_b[] = $val;
			}
			$where[$key] = $_w;
		}
		return array($where, $_b);
	}
  

/**
 * 検索条件のLIKEを整形する
 * @param string $opera 検索方法: like, %like, like%, %like%
 * @param array/string  $_b 検索値
 * @return array
 */
	protected static function _check_like($opera, $_b) {
		$hash_table = array(
			"like" => function($bind) {
				return array_map(function($i) {
                    return "%" . $i . "%";
                }, $bind);
			},
			"like%" => function($bind) {
				return array_map(function($i) {
                    return $i . "%";
                }, $bind);
			},
			"%like" => function($bind) {
				return array_map(function($i) {
                    return "%" . $i;
                }, $bind);
			}
		);
		if(isset($hash_table[$opera])) {
			return array(
				"like",
				call_user_func($hash_table[$opera], $_b)
			);
		} else {
			return array($opera, $_b);
		}
	}

/**
 * SQL文の検索条件を直指定する
 * @param string $where_condition SQL文の検索条件
 * @param boolean $filter 持続的にするかどか
 * @return void
 */
	public function where($where_condition, $filter = false) {
		if($filter) {
			$this->where_filter[] = $where_condition;
		} else {
			$this->where_condition[] = $where_condition;
		}
	}


/**
 * 設定したSQL文の検索条件を取得する
 * @return array
 */
	protected function _where() {
		$where = array_merge($this->where_filter, $this->where_condition);
		foreach($where as $wc) {
			$this->where[] = "(" . $wc . ")";
		}
	}

/**
 * 更新条件を設定する
 * @param string $set 更新カラム
 * @param mix $bind 更新値
 * @return $this;
 */
	public function set($set,$bind=null){
		$this->set[] = "`" . $set . "`";
		$_bind = (array) $bind;
		if(empty($_bind)) {
			$_bind = array($bind);
		}
		$this->set_args = array_merge($this->set_args, $_bind);
		return $this;
	}

/**
 * ソート条件設定
 * @param string $order ソート条件
 * @return $this
 */
	public function order($order){
		$order = $this->escape($order);
		$this->order = "ORDER BY {$order}";
		return $this;
	}

/**
 * グループ情報設定
 * @param string $group グループ情報
 * @return $this
 */
	public function group($group){
		$group = $this->escape($group);
		$this->group = "GROUP BY {$group}";
		return $this;
	}
  

/**
 * リミット条件設定
 * $l1だけ設定される場合は制限値、$l2設定される場合、$l1はオフセット値になる
 * @param integer $l1 オフセット/制限値 
 * @param integer $l2 制限値
 * @return $this
 */
	public function limit($l1 ,$l2=null){
		if(empty($l2)) {
			$this->limit = array($l1);
		} else {
			$this->limit = array($l1, $l2);
		}
		return $this;
	}
 
/**
 * inner_joinの別名
 * @return
 */
	public function join() {
		return call_user_func_array(array($this, "inner_join"), func_get_args());
	}
 

/**
 * モデルを双方向で結合させる
 * @return
 */
	public function inner_join() {
		list($target1, $target2, $col1, $col2) = $this->set_join_request(func_get_args());
		list($join, $from, $target) = $this->set_join_table($target1, $target2);
		$this->set_join_meta($from, $col1, $col2, $join, "INNER JOIN");
		return $this;
	}

/**
 * モデルを往方向で結合させる
 * @return
 */
	public function left_join() {
		list($target1, $target2, $col1, $col2) = $this->set_join_request(func_get_args());
		list($join, $from, $target) = $this->set_join_table($target1, $target2);
		$this->set_join_meta($from, $col1, $col2, $join, "LEFT JOIN");
		return $this;
	}

/**
 * モデルを復方向で結合させる
 * @return
 */
	public function right_join() {
		list($target1, $target2, $col1, $col2) = $this->set_join_request(func_get_args());
		list($join, $from, $target) = $this->set_join_table($target1, $target2);
		$this->set_join_meta($from, $col1, $col2, $join, "RIGHT JOIN");
		return $this;
	}


/**
 * 結合情報を整形する
 * @param array $argv 結合情報
 * @return
 */
	private function set_join_request($argv) {
		switch(count($argv)) {
        case 4:
            list($target1, $target2, $col1, $col2) = $argv;
            break;
        case 3:
            $target1 = $this;
            list($target2, $col1, $col2) = $argv;      
            break;
        case 2:
            $target1 = $this;
            $target2 = $argv[0];
            $col1 = $col2 = $argv[1];
            break;
		}
		return array($target1, $target2, $col1, $col2);
	}


/**
 * 結合テーブルの前後関係を決定する
 * @param object $target1 モデル１
 * @param object $target2 モデル２
 * @return
 */
	private function set_join_table($target1, $target2) {
		$table1 = $target1->getTable(true);
		$table2 = $target2->getTable(true);
		$this->relate_columns[$table1] = $target1->columns();
		$this->relate_columns[$table2] = $target2->columns();
	  
		if(isset($this->join[$table1]) || $table1 === $this->from) {
			$join=array(
				"from_table"=>$table1,
				"target_table"=>$table2,
			);
			$from = $target1;
			$target = $target2;
			$target_table = $table2;
		} else {
			$join=array(
				"from_table"=>$table2,
				"target_table"=>$table1,
			);
			$from = $target2;
			$target = $target1;
			$target_table = $table1;
		}
		$this->join_columns = array_merge($this->join_columns, $from->columns());
		$target_columns = $target->columns();
		//$intersect = array_intersect($this->join_columns, $target_columns);
		foreach($target_columns as $_col) {
			$_alias = "alias_" . str_replace('`', '', $target_table) . '_' . $_col;
			$_col = $target_table . '.`' . $_col . '`';
			$this->alias($_col, $_alias);
		}
		$this->join_columns = array_merge($this->join_columns, $target_columns);
		//join table filter check
		/**
		 * todo: find: $where => find: $alias
		 */
		foreach($target->get_all_filter() as $filter) {
			list($where, $bind, $opera) = $filter;
			$where = $target_table . "." . $where;
			$this->find($where, $bind, $opera);
		}
		return array($join, $from, $target);
	}
  

/**
 * 結合情報の最終整形、結合するカラムの前後関係を決定する
 * @param object $from 結合元モデル
 * @param string $col1 結合カラム名1
 * @param string $col2 結合カラム名2
 * @param array $join 整形中結合情報
 * @param string $type 結合方向
 * @return array $join 整形済み結合情報
 */
	private function set_join_meta($from, $col1, $col2, $join, $type) {
		$from_columns = $from->columns();
		$from_table = $from->getTable(true);
		$check_from = in_array($col1, $from_columns) ? true : false;
		if($check_from || $col1 === $col2) {
			$join["from_column"] = $col1;
			$join["target_column"] = $col2;
		} else {
			$join["from_column"] = $col2;
			$join["target_column"] = $col1;        
		}
		$join["join_type"] = $type;
		$this->join[$from_table . "_" . $join["target_table"]] = $join;
	}
  

/**
 * 整形済み結合情報を取得する
 * @return array
 */
	public function get_join() {
		return $this->join;
	}


/**
 * 検索用出力カラム取得
 * @return string 
 */
	private function select_column() {
		if(empty($this->alias)) {
			return "*";
		}
		$select = $this->make_all_column();
		return join(", ", $select);
	}


/**
 * 検索用出力カラムの整形
 * @param boolean $use_alias 別名を利用するかしないか
 * @return array
 */
	private function make_all_column($use_alias = true) {
		$select = $this->make_column($this->from, $this->columns(), $use_alias);
		$fored = array($this->from);
		foreach($this->join as $join) {
			$from = $join["from_table"];
			$target = $join["target_table"];
			if(!in_array($from, $fored)) {
				$select = array_merge($select, $this->make_column($from, $this->relate_columns[$from], $use_alias));
				$fored[] = $from;
			}
			if(!in_array($target, $fored)) {
				$select = array_merge($select, $this->make_column($target, $this->relate_columns[$target], $use_alias));	
				$fored[] = $target;
			}
		}
		return $select;
	}


/**
 * 出力カラムを`テーブル`.`カラム`という形式に整形する
 * @param string $from テーブル名
 * @param string $cols カラム名
 * @param boolean $use_alias 別名をしようするかしないか
 * @return
 */
	private function make_column($from, $cols, $use_alias = true) {
		if($use_alias) {
			foreach($cols as $key => $col) {
				$_col = $from . '.`' . $col . '`';
				if(isset($this->alias[$_col])) {
					$cols[$key] = $_col . " as " . $this->alias[$_col];
				} else {
					$cols[$key] = $_col;
				}
			}
		} else {
			foreach($cols as $key => $col) {
				$_col = $from . '.`' . $col . '`';
				$cols[$key] = $_col;
			}
		}
		return $cols;
	}
  
  
/**
 * 検索用SQL文を構成し、発行する
 * @api
 * @return $this
 */
	public function select(){
		$set = func_get_args();
		foreach($set as $_k => $_v) {
			$set[$_k] = $this->escape($_v);
		}
		$this->check_relation();
		$halfSql = array("SELECT", empty($set) ? $this->select_column() : join(",", $set), "FROM " . $this->from);
		foreach($this->join as $join) {
			$halfSql[] = $join["join_type"] . " " . $join["target_table"] . " ON " . $join["from_table"] . ".`".$join["from_column"]."` = ".$join["target_table"].".`".$join["target_column"]."`";
		}
		$sql = $this->build_where($halfSql);
		$this->query($sql, $this->args);
		return $this->reset();
	}

/**
 * 新規用SQL文を構成し、発行する
 * @api
 * @param array $args 直指定するプリペア値
 * @return $this
 */
	public function insert(){
		$this->args = array_merge($this->set_args, $this->args);
		$_set = join(",", $this->set);
		$sql = join(" ", array(
            "INSERT INTO",
            $this->from,
            "(" . $_set . ")",
            "VALUES",
            "(" . preg_replace("/[^,]++/", "?", $_set) . ")"));
		$this->query($sql, $this->args);
		return $this->reset();
	}


/**
 * 更新用SQL文を構成し、発行する
 * @api
 * @param boolean $active_record アクティブレコードからの更新かどか
 * @return
 */
	public function update(){
		$this->args = array_merge($this->set_args, $this->args);
		$set = array();
		$target = $this->from;
		foreach($this->set as $item){
			if(strpos($item, "=") === false){
				$item = $item . "=?";
			}
			$set[] = $item;
		}
		$halfSql = array(
			"UPDATE",
			$target,
			"SET",
			join(",", $set));
		$sql = $this->build_where($halfSql);
		$this->query($sql, $this->args);
		return $this->reset();
	}

/**
 * 削除用SQL文を構成し、発行する
 * @api
 * @param array $args 直指定するプリペア値 
 * @return
 */
	public function delete($args = null){
		$halfSql = array();
		$halfSql[] = "DELETE FROM";
		$halfSql[] = $this->from;
		$sql = $this->build_where($halfSql);
		$this->args = array_merge($this->args, (array)$args);
		$this->query($sql, $this->args);
		return $this->reset();
	}

/**
 * 検索条件SQL文を最終構成
 * @param string $halfSql 検索・新規・更新・削除SQLのWHERE以前部分 
 * @return
 */
	protected function build_where($halfSql){
		foreach($this->find as $obj){
			$this->_find($obj[0],$obj[1],$obj[2]);
		}
		foreach($this->get_all_filter() as $key => $obj){
			$this->_find($obj[0],$obj[1],$obj[2]);
		}
		$this->_where();
		if(!empty($this->where)){
			$halfSql[]="WHERE ".join(" AND ",$this->where);
		}
		$halfSql[] = join(" ", array($this->group, $this->order));
		if(!empty($this->limit)){
			$halfSql[]="LIMIT ".join(",",$this->limit);
		}
		return join(" ",$halfSql);
	}


/**
 * クエリ発行中に一時に利用した情報を解放する
 * @return $this
 */
	protected function reset(){
		$this->alias = $this->limit = $this->where_condition = $this->where = $this->join = $this->find = $this->set_args = $this->set = $this->args = array();
		$this->order = $this->group = null;
		return $this;
	}

/**
 * 数字や数字の文字列以外を全てエスケープする(Mysql)
 * @param mix $val エスケープ目標
 * @param string $quote
 * @return mix エスケープ済み値
 */
	public function escape($val, $quote = "") {
		if(!is_numeric($val)) {
			$val = self::$conn->quote($val);
			if(empty($quote)) {
				$val = substr($val, 1, -1);
			}
		}
		return $val;
	}

/**
 * 目標文字列に「`」で囲む
 * @param string $str 目標文字列
 * @return string
 */
	public function quote($str) {
		return '`' . $str . '`';
	}
}


<?php
namespace Framework\Core\Model\SqlBuilder;
use Framework\Core\Interfaces\Model\SchemaInterface;
use Framework\Core\Interfaces\Model\SqlBuilderInterface;

class MySql implements SqlBuilderInterface
{
    const ERROR_INVALID_PARAMETER_FOR_BETWEEN = "error: invalid parameter for between in column [%s]";

    private $Schema = null;
    private $sql = null;
	private $parameters = [];
    
	private $set = [];
	private $set_args = [];
	private $from = null;
	private $table = null;
	private $findStack = [];
	private $joinStack = [];
	private $whereStack = [];
	private $orderStack = [];
    private $orderQuery = null;
    private $groupStack = [];
    private $groupQuery = null;
    private $replaceStack = [];
    private $replaceQuery = null;
    private $replaceParameters = [];
	private $limit = [];
	private $alias = [];

    /**
     * クエリ発行中に一時に利用した情報を解放する
     * @return $this
     */
	private function reset(){
        
		return $this;
	}

    public function setSchema(SchemaInterface $Schema)
    {
        $this->Schema = $Schema;
        $this->setTable($Schema->getName());
    }

    public function getSchema()
    {
        return $this->Schema;
    }

	private function setTable($table){
		$this->table = $table;
		$this->from = '`' . $table . '`';
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

    public function findBy($dataSet)
    {
        foreach($dataSet as $set) {
            $column = $set[0];
            $bind = isset($set[1]) ? $set[1] : null;
            $opera = isset($set[2]) ? $set[2] : "=";
            $this->find($column, $bind, $set);
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
	public function find($column, $bind = null, $opera = "="){
        $this->checkColumn($column);
        $opera = strtolower($opera);
		$this->findStack[]=array($column, $bind, $opera);
		return $this;
	}

    /**
     * 遅的にバンドする 
     * 設定した検索条件をSQL文に変換する
     * @param max 　　$where 検索カラム名(集)
     * @param mix 　　$bind  検索値(集)
     * @param string $opera 検索方法
     * @return
     */
	private function execFind($column, $value = null, $opera = "="){
		if(empty($value)) {
            //make is null or is not null
			return false;
		}
        $where = null;
        $column = $this->makeQueryColumn($column);
        switch($opera) {
        case "between":
            if(count($value) !== 2) {
                throw new Exception(sprintf(self::ERROR_INVALID_PARAMETER_FOR_BETWEEN, $column));
            }
            $where = '(' . $column . ' BETWEEN ? AND ?)';
            break;
        case "=":
            if(is_array($value)) {
                
                
            } else {
                $where = "(" . $column . " = ?)";
            }
            break;
        case "<>":
        case "not":
            if(is_array($value)) {
                
                
            } else {
                $where = "(" . $column . " <> ?)";
            }
            break;
        case "like":
            $where = "(" . $column . " like ?)";
            break;
        case "%like":
            $where = "(" . $column . " like ?)";
            break;
        case "like%":
            $where = "(" . $column . " like ?)";
            break;
        default:
            $where = "(" . $column . " " . $this->escape($opera) . " ?)";
            break;
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
	public function addOrder($column, $order){
        $this->checkColumn($column);
		$order = $this->escape($order);
        $this->orderStack[] = $column . " " . $order;
		return $this;
	}

    public function setOrder($orderQuery)
    {
        $this->orderQuery = "ORDER BY " . $orderQuery;
    }

    public function getOrder()
    {
        if($this->orderQuery === null) {
            $this->orderQuery = "ORDER BY " . join(",", $this->orderStack);
        }
        return $this->orderQuery;
    }

/**
 * グループ情報設定
 * @param string $group グループ情報
 * @return $this
 */
	public function addGroup($column)
    {
        $this->checkColumn($column);
		$column = $this->escape($column);
        $this->groupStack[] = $column;
        return $this;
	}

    public function setGroup($groupQuery)
    {
        $this->groupQuery = "GROUP BY " . $groupQuery;
    }
  
    public function getGroup()
    {
        if($this->groupQuery === null) {
            $this->groupQuery = "GROUP BY " . join(",", $this->groupStack);
        }
        return $this->groupQuery;
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
	public function join($joinModel, $leftCol, $rightCol = null) {
        return $this->innerJoin($joinModel, $leftCol, $rightCol);
	}
 

/**
 * モデルを双方向で結合させる
 * @return
 */
	public function innerJoin($joinModel, $leftCol, $rightCol = null) {
		list($target1, $target2, $col1, $col2) = $this->set_join_request($joinModel, $leftCol, $rightCol);
		list($join, $from, $target) = $this->set_join_table($target1, $target2);
		$this->set_join_meta($from, $col1, $col2, $join, "INNER JOIN");
		return $this;
	}

/**
 * モデルを往方向で結合させる
 * @return
 */
	public function leftJoin($joinModel, $leftCol, $rightCol = null) {
		list($target1, $target2, $col1, $col2) = $this->set_join_request($joinModel, $leftCol, $rightCol);
		list($join, $from, $target) = $this->set_join_table($target1, $target2);
		$this->set_join_meta($from, $col1, $col2, $join, "LEFT JOIN");
		return $this;
	}

/**
 * モデルを復方向で結合させる
 * @return
 */
	public function rightJoin($joinModel, $leftCol, $rightCol = null) {
		list($target1, $target2, $col1, $col2) = $this->set_join_request($joinModel, $leftCol, $rightCol);
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
	private function build_where($halfSql){
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
     * 数字や数字の文字列以外を全てエスケープする(PDO::quote)
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

    public function getQuery()
    {
        return $this->sql;
    }
    
    public function getParameters()
    {
        return $this->args;
    }

    public function getSubQuery()
    {

    }

    public function setSubQuery($subQuery)
    {
        
    }

    public function having($subQuery)
    {
        
    }
    
    public function addReplace($column, $search, $replace)
    {
        $this->checkColumn($column);
        $this->replaceStack[] = [$column, $search, $replace];
        $this->replaceQuery = null;
    }

    public function getReplace()
    {
        if($this->replaceQuery === null) {
            $replaceQuery = [];
            $replaceParameters = [];
            foreach($this->replaceStack as $replace) {
                [$column, $search, $replace] = $replace;
                $replaceQuery[] = sprintf("SET %s = REPLACE(%s, ?, ?)", $column, $column);
                $replaceParameters[] = $search;
                $replaceParameters[] = $replace;
            }
            $replaceQuery = join(", ", $replaceQuery);
            $this->replaceQuery = $replaceQuery;
            $this->replaceParameters = $replaceParameters;
        }
        return [$this->replaceQuery, $this->replaceParameters];
    }

    public function checkColumn($column)
    {
        return $this->getSchema()->checkColumn($column);
    }

    public function makeQueryColumn($column)
    {
        return $column;
    }
}


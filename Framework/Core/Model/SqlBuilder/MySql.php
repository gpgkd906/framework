<?php
namespace Framework\Core\Model\SqlBuilder;
use Framework\Core\Interfaces\Model\SchemaInterface;
use Framework\Core\Interfaces\Model\SqlBuilderInterface;
use PDO;
use Exception;

class MySql implements SqlBuilderInterface
{
    const ERROR_INVALID_PARAMETER_FOR_BETWEEN = "error: invalid parameter for between in column [%s]";
    const ERROR_INVALID_OPERA = "error: ERROR_INVALID_OPERA, column [%s] on invalid opera [%s]";
    const ERROR_INVALID_WHERE_CONDITION = "error: INVALID_WHERE_CONDITION, column [%s] on invalid [%s]";
    const ERROR_INVALID_CONNECTION_INFO = "error: ERROR_INVALID_CONNECTION_INFO";
    const ERROR_INVALID_SQL = "error: ERROR_INVALID_SQL";
    
    private $Schema = null;
    private $sql = null;
	private $parameters = [];
    
	private $from = null;
	private $table = null;
	private $setColumnStack = [];
    private $setValueStack = [];
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
    private $limitQuery = null;
	private $alias = [];
    static private $connection = null;
    static private $connectionInfo = null;

    public function setConnectionInfo($connectionInfo)
    {
        if(self::$connectionInfo == null) {
            if(empty($connectionInfo["host"]) || empty($connectionInfo["user"])) {
                throw new Exception(self::ERROR_INVALID_CONNECTION_INFO);
            }
            if(!isset($connectionInfo["database"]) || empty($connectionInfo["database"])) {
                $connectionInfo["database"] = "mysql";
            }
            if(!isset($connectionInfo["charset"]) || empty($connectionInfo["charset"])) {
                $connectionInfo["charset"] = "utf8";
            }
            self::$connectionInfo = $connectionInfo;
        }
    }

    static public function getConnection()
    {
        if(self::$connection === null) {
            if(self::$connectionInfo === null) {
                throw new Exception(self::ERROR_INVALID_CONNECTION_INFO);
            }
            $db = self::$connectionInfo["database"];
            $host = self::$connectionInfo["host"];
            $user = self::$connectionInfo["user"];
            $pass = self::$connectionInfo["password"];
            $dbname = self::$connectionInfo["dbname"];
            $charset = self::$connectionInfo["charset"];
            $connectStatement = sprintf("%s:host=%s;dbname=%s;charset=%s", $db, $host, $dbname, $charset);
            self::$connection = new PDO($connectStatement, $user, $pass);
        }
        return self::$connection;
    }

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
  
   /**
    * モデルのテーブル名を取得
    * @api
    * @return string 
    */
	public function getTable($quoted = false){
        if($quoted) {
            return $this->from;
        } else {
            return $this->table;
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
	public function find($column, $bind = null, $opera = "=", $junction = "AND"){
        $this->checkColumn($column);
        $opera = strtolower($opera);
		$this->findStack[] = array($column, $bind, $opera, $junction);
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
        $where = null;
        $column = $this->getSchema()->getFormatColumn($column);
		if(empty($value)) {
			$where = $this->execNull($column, $value, $opera);
		} else {
            switch($opera) {
            case "between":
                if(count($value) !== 2) {
                    throw new Exception(sprintf(self::ERROR_INVALID_PARAMETER_FOR_BETWEEN, $column));
                }
                $where = '(' . $column . ' BETWEEN ? AND ?)';
                $this->parameters = array_merge($this->parameters, $value);
                break;
            case "=": case "not": case "<>": case "!=":
                return $this->execEqual($column, $value, $opera);
                break;
            case "like": case "%like": case "like%": case "not like":
                return $this->execLike($column, $value, $opera);
                break;
            default:
                $where = "(" . $column . " " . $this->escape($opera) . " ?)";
                $this->parameters[] = $value;
                break;
            }
        }
        if($where === null) {
            throw new Exception(sprintf(self::ERROR_INVALID_WHERE_CONDITION, $column, $opera));
        }
        return $junction . " " . $where;
	}

    private function execNull($column, $value, $opera)
    {
        switch($opera) {
        case "=":
            $where = "(" . $column . " IS NULL)";
            break;
        case "<>": case "not": case "!=":
            $where = "(" . $column . " IS NOT NULL)";
            break;
        default:
            throw new Exception(sprintf(self::ERROR_INVALID_OPERA, $column, $opera));
            break;
        }
        return $where;
    }


    private function execEqual($column, $value, $opera)
    {
        switch($opera) {
        case "=":
            if(is_array($value)) {
                $where = "(" . $column . " in (" . join(", ", array_pad([], count($value), "?")) . "))";                
                $this->parameters = array_merge($this->parameters, $value);
            } else {
                $where = "(" . $column . " = ?)";
                $this->parameters[] = $value;
            }
            break;
        case "<>": case "not": case "!=":
            if(is_array($value)) {
                $where = "(" . $column . " not in (" . join(", ", array_pad([], count($value), "?")) . "))";
                $this->parameters = array_merge($this->parameters, $value);
            } else {
                $where = "(" . $column . " <> ?)";
                $this->parameters[] = $value;
            }
            break;
        default:
            throw new Exception(sprintf(self::ERROR_INVALID_OPERA, $column, $opera));
            break;
        }
        return $where;
    }

    private function execLike($column, $value, $opera)
    {
        switch($opera) {
        case "%like":
            $where = "(" . $column . " like ?)";
            $this->parameters[] = $value;
            break;
        case "like%":
            $where = "(" . $column . " like ?)";
            $this->parameters[] = $value;
            break;
        case "like":
            $where = "(" . $column . " like ?)";
            $this->parameters[] = $value;
            break;
        default:
            throw new Exception(sprintf(self::ERROR_INVALID_OPERA, $column, $opera));
            break;
        }
        return $where;
    }
        
  
    /**
     * 更新条件を設定する
     * @param string $set 更新カラム
     * @param mix $bind 更新値
     * @return $this;
     */
	public function set($column, $value){
        $this->checkColumn($column);
        $this->setColumnStack[] = $column;
        $this->setValueStack[] = $value;
		return $this;
	}

    /**
     * ソート条件設定
     * @param string $order ソート条件
     * @return $this
     */
	public function addOrder($column, $order){
        $this->checkColumn($column);
        $column = $this->getSchema()->getFormatColumn($column);
		$order = strtoupper($this->escape($order));
        $this->orderStack[] = $column . " " . $order;
        $this->orderQuery = null;
		return $this;
	}

    public function setOrder($orderQuery)
    {
        $this->orderQuery = $orderQuery;
    }

    public function getOrder()
    {
        if($this->orderQuery === null) {
            if(!empty($this->orderStack)) {
                $this->orderQuery = "ORDER BY " . join(",", $this->orderStack);
            }
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
		$column = $this->getSchema()->getFormatColumn($column);
        $this->groupStack[] = $column;
        $this->groupQuery = null;
        return $this;
	}

    public function setGroup($groupQuery)
    {
        $this->groupQuery = $groupQuery;
    }
  
    public function getGroup()
    {
        if($this->groupQuery === null) {
            if(!empty($this->groupStack)) {
                $this->groupQuery = "GROUP BY " . join(",", $this->groupStack);
            }
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
	public function limit($l1, $l2=null){
		if(empty($l2)) {
			$this->limit = array($l1);
		} else {
			$this->limit = array($l1, $l2);
		}
        $this->limitQuery = null; 
		return $this;
	}

    public function getLimit()
    {
		if($this->limitQuery === null){
            if(!empty($this->limit)) {
                $this->limitQuery = "LIMIT " . join(",", $this->limit);
            }
		}
        return $this->limitQuery;
    }
    
    /**
     * inner_joinの別名
     * @return
     */
	public function join($Schema, $from, $to) {
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
  
    private function execJoin()
    {
        $joinQuery = [];
		foreach($this->joinStack as $join) {
			$joinQuery[] = $join["join_type"] . " " . $join["target_table"] . " ON " . $join["from_table"] . ".`".$join["from_column"]."` = ".$join["target_table"].".`".$join["target_column"]."`";
		}
        return join(" ", $joinQuery);
    }
  
/**
 * 検索用SQL文を構成し、発行する
 * @api
 * @return $this
 */
	public function select($columns){
		foreach($columns as $key => $col) {
			$this->checkColumn($key);
		}
		$sql = array("SELECT", join(", ", $columns), "FROM " . $this->getTable(true));
        $sql[] = $this->execJoin();
        $sql[] = $this->execWhere();
        $sql[] = $this->getGroup();
        $sql[] = $this->getOrder();
        $sql[] = $this->getLimit();
        $this->sql = join(" ", $sql);
		return $this->reset();
	}

    /**
     * 新規用SQL文を構成し、発行する
     * @api
     * @param array $args 直指定するプリペア値
     * @return $this
     */
	public function insert(){
        $Schema = $this->getSchema();
        $columnStack = [];
        foreach($this->setColumnStack as $key) {
            $columnStack[] = $Schema->getColumn($key);
        }
        $valueStack = array_pad([], count($this->setValueStack), "?");
        $this->sql = join(" ", [
            "INSERT INTO",
            $this->getTable(true),
            "(" . join(", ", $columnStack) . ")",
            "VALUES",
            "(" . join(", ", $valueStack) . ")"]);
        $this->parameters = array_merge($this->setValueStack, $this->parameters);
		return $this->reset();
	}


    /**
     * 更新用SQL文を構成し、発行する
     * @api
     * @param boolean $active_record アクティブレコードからの更新かどか
     * @return
     */
	public function update(){
		$this->sql = join(" ", [
			"UPDATE",
			$this->getTable(true),
			"SET",
            $this->makeUpdateParts(),
            $this->execWhere()
        ]);
        $this->parameters = array_merge($this->setValueStack, $this->parameters);
		return $this->reset();
	}

/**
 * 削除用SQL文を構成し、発行する
 * @api
 * @param array $args 直指定するプリペア値 
 * @return
 */
	public function delete(){
		$sql = array();
		$sql[] = "DELETE FROM";
		$sql[] = $this->getTable(true);
		$sql[] = $this->execWhere();
        $this->sql = join(" ", $sql);
		return $this->reset();
	}

    public function makeUpdateParts()
    {
        $columnStack = [];
        $Schema = $this->getSchema();
        foreach($this->setColumnStack as $key) {
            $columnStack[] = $Schema->getFormatColumn($key) . " = ?";
        }
        return join(", ", $columnStack);
    }

    /**
     * 検索条件SQL文を最終構成
     * @return
     */
	private function execWhere(){
        $whereQuery = [];
        foreach($this->findStack as $key => $find) {
            if($key > 0) {
                $whereQuery[] = $find[3];
            }
            $whereQuery[] = $this->execFind($find[0], $find[1], $find[2]);
        }
		return " WHERE " . join(" ", $whereQuery);
	}
    
    /**
     * 数字や数字の文字列以外を全てエスケープする(PDO::quote)
     * @param mix $val エスケープ目標
     * @param string $quote
     * @return mix エスケープ済み値
     */
	public function escape($val, $quote = "") {
		if(!is_numeric($val)) {
			$val = self::getConnection()->quote($val);
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

    public function getSql()
    {
        return $this->sql;
    }

    public function setSql($sql = null)
    {
        $this->sql = $sql;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters($parameters = [])
    {
        $this->parameters = $parameters;
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
                list($column, $search, $replace) = $replace;
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

    public function query($sql = null, $parameters = null)
    {
        if($sql == null && $parameters == null) {
            $sql = $this->getSql();
            $parameters = $this->getParameters();
            $this->setSql(null);
            $this->setParameters([]);
        }
        if($sql === null) {
            throw new Exception(self::ERROR_INVALID_SQL);
        }
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($parameters);
        return $stmt;
    }
}


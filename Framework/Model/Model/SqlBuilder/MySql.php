<?php
namespace Framework\Model\Model\SqlBuilder;
use Framework\Model\Model\SchemaInterface;
use Framework\Model\Model\SqlBuilderInterface;
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
    private $lastQuery = null;
    
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
            $host = self::$connectionInfo["host"];
            $user = self::$connectionInfo["user"];
            $pass = self::$connectionInfo["password"];
            $dbname = self::$connectionInfo["dbname"];
            $charset = self::$connectionInfo["charset"];
            $connectStatement = sprintf("mysql:host=%s;dbname=%s;charset=%s", $host, $dbname, $charset);
            self::$connection = new PDO($connectStatement, $user, $pass);
        }
        return self::$connection;
    }

    /**
     * クエリ発行中に一時に利用した情報を解放する
     * @return $this
     */
	private function reset(){
        $this->setColumnStack = [];
        $this->setValueStack = [];
        $this->findStack = [];
        $this->joinStack = [];
        $this->whereStack = [];
        $this->orderStack = [];
        $this->orderQuery = null;
        $this->groupStack = [];
        $this->groupQuery = null;
        $this->replaceStack = [];
        $this->replaceQuery = null;
        $this->replaceParameters = [];
        $this->limit = [];        
        $this->limitQuery = null;
        $this->alias = [];
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
	public function limit($l1, $l2 = null){
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
	public function join(SchemaInterface $Schema, $from, $to) {
        return $this->innerJoin($Schema, $from, $to);
	}
 
    /**
     * モデルを双方向で結合させる
     * @return
     */
	public function innerJoin(SchemaInterface $Schema, $from, $to) {
        $this->joinStack[] = [
            "joinTable" => $Schema->getTable(),
            "fromColumn" => $this->getSchema()->getFormatColumn($from),
            "toColumn" => $Schema->getFormatColumn($to),
            "joinType" => "INNER JOIN",
            "Schema" => $Schema,
        ];
		return $this;
	}

    /**
     * モデルを往方向で結合させる
     * @return
     */
	public function leftJoin(SchemaInterface $Schema, $from, $to) {
        $this->joinStack[] = [
            "joinTable" => $Schema->getTable(),
            "fromColumn" => $this->getSchema()->getFormatColumn($from),
            "toColumn" => $Schema->getFormatColumn($to),
            "joinType" => "LEFT JOIN",
            "Schema" => $Schema,
        ];
		return $this;
	}

    /**
     * モデルを復方向で結合させる
     * @return
     */
	public function rightJoin(SchemaInterface $Schema, $from, $to) {
        $this->joinStack[] = [
            "joinTable" => $Schema->getTable(),
            "fromColumn" => $this->getSchema()->getFormatColumn($from),
            "toColumn" => $Schema->getFormatColumn($to),
            "joinType" => "RIGHT JOIN",
            "Schema" => $Schema,
        ];
		return $this;
	}

    public function getJoin()
    {
        return $this->joinStack;
    }
    
    private function execJoin()
    {
        $joinQuery = [];
		foreach($this->joinStack as $join) {
			$joinQuery[] = $join["joinType"] . " " . $join["joinTable"] . " ON " . $join["fromColumn"] . " = " . $join["toColumn"];
		}
        return join(" ", $joinQuery);
    }
  
    /**
     * 検索用SQL文を構成し、発行する
     * @api
     * @return $this
     */
	public function select($columns)
    {
		foreach($columns as $key => $col) {
			$this->checkColumn($key);
		}
		$sql = array("SELECT", join(", ", $columns), "FROM " . $this->getSchema()->getTable());
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
            $this->getSchema()->getTable(),
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
			$this->getSchema()->getTable(),
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
		$sql[] = $this->getSchema()->getTable();
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
                //AND, OR
                $whereQuery[] = $find[3];
            }
            $whereQuery[] = $this->execFind($find[0], $find[1], $find[2]);
        }
        if(empty($whereQuery)) {
            return "";
        } else {
            return " WHERE " . join(" ", $whereQuery);
        }
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
        $this->setLastQuery($sql, $parameters);
        return $stmt;
    }

    public function getLastQuery()
    {
        return $this->lastQuery;
    }

    private function setLastQuery($sql, $parameters)
    {
        $this->lastQuery = [$sql, $parameters];
    }
}


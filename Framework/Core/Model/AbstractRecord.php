<?php

namespace Framework\Core\Model;

use Framework\Core\Interfaces\Model\RecordInterface;
use Framework\Core\Interfaces\Model\ModelInterface;
use Framework\Core\Interfaces\Model\SchemaInterface;
use Framework\Core\Interfaces\EventInterface;
use Exception;

abstract class AbstractRecord implements RecordInterface, EventInterface
{
    use \Framework\Core\EventManager\EventTrait;

    const ERROR_INVALID_RECORD = "error: invalid record";
    const ERROR_INVALID_MODEL = "error: invalid model";
    const ERROR_INVALID_COLUMN_FOR_SET = "error: INVALID_COLUMN_FOR_SET [%s]";
    const ERROR_NONE_WRITABLE = "error: this is a dirty-record[a partially record or a deleted record]";
    const ERROR_PRIMARY_KEY_IS_CHANGED = "error: PRIMARY_KEY_IS_CHANGED";
    const ERROR_DEFINE_SCHEMA_IN_RECORD = "error: DEFINE_SCHEMA_IN_RECORD";

    /**
	 * テーブルカラムにあるデータセット
	 * @api
	 * @var Array
	 * @link
	 */
	private $store = array();

    /**
	 * プライマリキーの値
	 * @api
	 * @var Mixed
	 * @link
	 */
	private $primaryValue = null;
    
    /**
	 * レコードが実際に変更されたかどか
	 * @api
	 * @var 
	 * @link
	 */
	private $realChanged = false;

    static public $config = [
        "Model" => null,
    ];

    const TRIGGER_SAVE = "Save";
    
    protected $relations = [];
    
    private $isDirty = false;
    
    /**
	 * ActiveRecord構造関数
	 * @api
	 * @param Array $row パッケージするデータセット  
	 * @return
	 * @link
	 */
	public function __construct($isDirty = false, $initStore = null)
    {
        $this->triggerEvent(self::TRIGGER_INIT);
        $this->isDirty = $isDirty;
        $this->store = static::getFormat();
        if($initStore !== null) {
            $this->assign($initStore);
        }
        $this->realChanged = false;
        $this->triggerEvent(self::TRIGGER_INITED);
	}

    static public function getModel()
    {
        if(!static::$config["Model"]) {
            throw new Exception(self::ERROR_INVALID_MODEL);
        }
        if(is_string(static::$config["Model"])) {
            if(!class_exists(static::$config["Model"])) {
                throw new Exception(self::ERROR_INVALID_MODEL);
            }
            $modelLabel = static::$config["Model"];
            $Model = $modelLabel::getSingleton();
            if($Model instanceof ModelInterface) {
                static::$config["Model"] = $Model;
            } else {
                throw new Exception(self::ERROR_INVALID_MODEL);
            }
        }
        return static::$config["Model"];
    }

    static public function getSchema()
    {
        if(!isset(static::$config["Schema"])) {
            static::$config["Schema"] = static::getModel()->getSchema();
        }
        if(isset(static::$config["Schema"]) && is_string(static::$config["Schema"])) {
            throw new Exception(self::ERROR_DEFINE_SCHEMA_IN_RECORD);
        }
        return static::$config["Schema"];
    }
    
    static public function getFormat()
    {
        if(!isset(static::$config["Format"])) {
            static::$config["Format"] = array_fill_keys(static::getSchema()->getObjectKeys(), "");
        }
        return static::$config["Format"];
    }

    public function isDirty()
    {
        return $this->isDirty;
    }

	public function get($name)
    {
		return isset($this->store[$name]) ? $this->store[$name] : null;
	}
    
    /**
	 * データセットにキーを指定して更新する
	 * @api
	 * @param String $name データキー
	 * @param Mixed $value 値
	 * @return
	 * @link
	 */
	public function set($name, $value)
    {
        if(!isset($this->store[$name])) {
            throw new Exception(sprintf(self::ERROR_INVALID_COLUMN_FOR_SET, $name));
        }
		if($this->store[$name] !== $value) {
			$this->realChanged = true;
		}
		return $this->store[$name] = $value;
	}
	
    /**
	 * 主キー値取得
	 * @api
	 * @return
	 * @link
	 */
	public function getPrimaryValue()
    {
		return $this->primaryValue;
	}

    private function setPrimaryValue($primaryValue)
    {
        $this->primaryValue = $primaryValue;
    }
	
	/**
	 * データ一括更新
	 * @api
	 * @param Array $data 更新するデータ
	 * @return
	 * @link
	 */
	public function assign($data)
    {
		foreach($data as $name => $value) {
			$this->set($name, $value);
		}

        $primaryKey = static::getSchema()->getObjectPrimaryKey();
		if(isset($data[$primaryKey])) {
			$this->setPrimaryValue($data[$primaryKey]);
		}

	}

    /**
     * レコードのupdate_dtだけ更新する
     * タイムスタンプを利用していなければ，touchメソッドは何もしません
     * @api
     * @param   
     * @param    
     * @return
     * @link
     */
    public function touch ()
    {
        if(static::getSchema()->hasTimeStamp("updateDate")) {
            $this->realChanged = true;
            $this->save();
        }
    }
    
    /**
	 * データセットを配列で取得
	 * @api
	 * @return
	 * @link
	 */
	public function toArray()
    {
		return $this->store;
	}
	
	/**
	 * データセットの変更をデータベースに反映
	 *
	 *値が実質的に変更されてない場合はSQLの生成及び発行を飛ばす：「最も早いSQLはSQLを発行しないこと」
	 * ※注意、明示的に値の変更がなくてもtouchメソッドがコールされるとタイムスタンプが更新されるのでSQLが発行される。
     *
	 *更新時の挙動は要検討、論理上プライマリキーは変更すべきものではない
	 * 
	 * * task: make primary value can not be changed
	 * @api
	 * @return
	 * @link
	 */
	public function save()
    {
        $this->triggerEvent(self::TRIGGER_SAVE);
        if($this->isDirty === true) {
            throw new Exception(self::ERROR_NONE_WRITABLE);
			return false;            
        }
		if(empty($this->store)) {
            throw new Exception(self::ERROR_INVALID_RECORD);
			return false;
		}
        $Schema = static::getSchema();
		$primaryKey = $Schema->getObjectPrimaryKey();
        $sqlBuilder = static::getModel()->getSqlBuilder();
		if(isset($this->primaryValue)) {
			if(!$this->realChanged) {
				return false;
			}
			if($Schema->hasTimeStamp("updateDate")) {
                $this->set($Schema->getObjectTimeStamp("updateDate"), date("Ymd", $_SERVER["REQUEST_TIME"]));
			}
			if($Schema->hasTimeStamp("updateTime")) {
                $this->set($Schema->getObjectTimeStamp("updateTime"), date("His", $_SERVER["REQUEST_TIME"]));
			}
            $sqlBuilder->find($primaryKey, $this->getPrimaryValue());
            foreach($this->store as $key => $value) {
                $sqlBuilder->set($key, $value);
            }
            $sqlBuilder->update()->query();
			$this->realChanged = false;
		} else {
			if($Schema->hasTimeStamp("createDate")) {
                $this->set($Schema->getObjectTimeStamp("createDate"), date("Ymd", $_SERVER["REQUEST_TIME"]));
            }
			if($Schema->hasTimeStamp("createTime")) {
                $this->set($Schema->getObjectTimeStamp("createTime"), date("His", $_SERVER["REQUEST_TIME"]));
			}
            foreach($this->store as $key => $value) {
                $sqlBuilder->set($key, $value);
            }
            $sqlBuilder->insert()->query();
            $this->setPrimaryValue($sqlBuilder->getLastId());
        }
		return $this->getPrimaryValue();
	}
	
	/**
	 * データ削除
	 * 
	 * @api
	 * @return
	 * @link
	 */
	public function delete()
    {
        if($this->isDirty === true) {
            throw new Exception(self::ERROR_NONE_WRITABLE);
			return false;            
        }
		if($this->getPrimaryValue()) {
			if(static::getModel()->find(static::getSchema()->getPrimaryKey(), $this->getPrimaryValue())->delete()) {
                $this->store = static::getFormat();
                $this->isDirty = true;
                return true;
            }
		}
	}
    
    public function getRelationRecord($relation)
    {
        if(!isset($this->relations[$relation])) {
            //sample: "table" => ["type" => "oneToMany", "from" => $from, "to" => $to]
            $relationConfig = static::getSchema()->getRelations();
            if(isset($relationConfig[$relation])) {
                $relationTarget = $relationConfig[$relation];
                $relationModel = $relationTarget["Model"]::getSingleton();
                
                $column = $relationTarget["to"];
                $value = $this->get($relationTarget["from"]);
                $relationModel->find($column, $value);
                switch($relationTarget["type"]) {
                case SchemaInterface::RELATION_TO_ONE:
                    $relationModel->limit(1);
                    $this->relations[$relation] = $relationModel->get();
                    break;
                case SchemaInterface::RELATION_TO_MANY:
                    $this->relations[$relation] = $relationModel->getAll();
                    break;
                default:
                    throw new Exception(sprintf(self::ERROR_INVALID_RELATION_TYPE, $relationConfig["type"], static::getSchema()->getName()));
                    break;
                }
            } else {
                throw new Exception(sprintf(self::ERROR_INVALID_RELATION, $relation, static::getSchema()->getName()));
            }
        }
        return $this->relations[$relation];
    }
}

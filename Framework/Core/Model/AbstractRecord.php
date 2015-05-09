<?php

namespace Framework\Core\Model;

use Framework\Core\Interfaces\Model\RecordInterface;
use Framework\Core\Interfaces\Model\ModelInterface;
use Exception;

abstract class AbstractRecord implements RecordInterface
{
    const ERROR_INVALID_RECORD = "error: invalid record";
    const ERROR_INVALID_MODEL = "error: invalid model";
    
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

    static private $Schema = null;

    static private $Model = null;
    
    public $config = [
        "Model" => null,
    ];
    /**
	 * ActiveRecord構造関数
	 * @api
	 * @param Array $row パッケージするデータセット  
	 * @return
	 * @link
	 */
	public function __construct($Model = null){
        if(self::$Model === null) {
            if($Model === null) {
                if(!$this->config["Model"] || !class_exists($this->config["Model"])) {
                    throw new Exception(self::ERROR_INVALID_MODEL);
                }
                $Model = $this->config["Model"]::getSingleton();
            }
            if($Model instanceof ModelInterface) {
                self::$Model = $Model;
            } else {
                throw new Exception(self::ERROR_INVALID_MODEL);
            }
        }
        if(self::$Schema === null) {
            self::$Schema = self::$Model->getSchema();
        }
	}
    
	public function get($name){
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
	public function set($name, $value){
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
	public function getPrimaryValue(){
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
	public function assign($data) {
		foreach($data as $name => $value) {
			$this->set($name, $value);
		}
        $primaryKey = self::$Schema->getPrimaryKey();
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
        if(self::$Schema->hasColumn("update_dt")) {
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
	public function toArray() {
		return $this->store;
	}
	
	/**
	 * データセットの変更をデータベースに反映
	 *
	 *値が実質的に変更されてない場合はSQLの生成及び発行を飛ばす：「最も早いSQLはSQLを発行しないこと」
	 * ※注意、明示的に値の変更がなくてもtouchメソッドがコールされるとタイムスタンプが更新されるので
     *   SQLが発行される。
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
            throw new Exception(self::ERROR_INVALID_RECORD);
			return false;
		}
		$primaryKey = self::$Schema->getPrimaryKey();
        $Model = self::$Model;
		if(isset($this->primaryValue)) {
			if(!$this->realChanged) {
				return false;
			}
			if(self::$Schema->hasColumn("update_dt")) {
				$this->set("update_dt", $_SERVER["REQUEST_TIME"]);
			}
            $Model->skip_filter();
			$Model->find($primaryKey, $this->getPrimaryValue())->put($this->store)->update(true);
			$this->real_changed = false;
		} else {
			if(self::$Schema->hasColumn("register_dt")) {
                $this->set("register_dt", $_SERVER["REQUEST_TIME"]);
                $this->set("update_dt", $_SERVER["REQUEST_TIME"]);
			}
			$Model->put($this->store)->insert();
            $this->setPrimaryValue($Model->getLastId());
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
	public function delete() {
		if($this->getPrimaryValue()) {
			if(self::$Model->find(self::$Schema->getPrimaryKey(), $this->getPrimaryValue())->delete()) {
                $this->store = array();
                return true;
            }
		}
	}
}

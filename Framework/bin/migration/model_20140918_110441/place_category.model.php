<?php
/**
 * place_category.model.php
 *
 *
 * myFramework : Origin Framework by Chen Han https://github.com/gpgkd906/framework
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
 * place_category_model
 * 
 * 場所のカテゴリデータベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class place_category_model extends model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','place_id','category'
    );
    /**
    * カラム定義
    * @api
    * @var array
    * @link
    */
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'place_id' => '`place_id` varchar(128) NOT NULL',
  'category' => '`category` int(11) NOT NULL',
);
    ##columns##
	##indexes##
    /**
    * インデックス定義
    * @api
    * @var array
    * @link
    */
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'place_id' => 'UNIQUE KEY `place_id` (`place_id`,`category`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`place_category`' => 'id');
    ##indexes##
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();


    /**
	 * 場所のカテゴリデータを保存
	 * @api 
	 * @param String $place_id Google Map用PlaceId
	 * @param Array $cate_ids カテゴリid(配列・複数)
	 * @return
	 * @link
	 */
	public function bind_category ($place_id, $cate_ids) {
		foreach($cate_ids as $category) {
			$this->create_record(array("place_id" => $place_id, "category" => $category));
		}
    }

}

/**
 * place_category_active_record
 * 
 * place_categoryデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class place_category_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
private static place_category = 'place_category';
/**
*
* プライマリキー
* @api
* @var 
* @link
*/
private static id = 'id';
/**
* モデルのカラムの反転配列。
* 
* 反転後issetが働ける、パフォーマンス的にいい
*
* 反転は自動生成するので，実行時に影響はありません
* @api
* @var 
* @link
*/
private static Array = array (
  'id' => 0,
  'place_id' => 1,
  'category' => 2,
);
###active_define###
}
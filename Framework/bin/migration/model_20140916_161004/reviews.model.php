<?php
class reviews_model extends model_core {
	##columns##
    public $columns = array(
        'id','author','content','place_id','entry','step','register_dt','update_dt'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'author' => '`author` int(11) NOT NULL',
  'content' => '`content` longtext NOT NULL',
  'place_id' => '`place_id` varchar(128) NOT NULL',
  'entry' => '`entry` int(11) NOT NULL',
  'step' => '`step` int(11) NOT NULL',
  'register_dt' => '`register_dt` bigint(20) NOT NULL',
  'update_dt' => '`update_dt` bigint(20) NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'reviews' => 'UNIQUE KEY `reviews` (`author`,`place_id`,`register_dt`)',
);
              public $primary_keys = array('`reviews`' => 'id');
    ##indexes##
	public $relation = array();


/**
 * 
 * @param string 
 * @param integer 
 * @param array 
 * @param resource 
 * @param object 
 * @param mix 
 * @return
 */
    public function append($raw, $author) {
		$review = $this->new_record();
		$review->author = $author;
		$review->content = $raw["review"];
		$review->place_id = $raw["place_id"];
		$review->entry = $raw["entry"];
		$review->step = $raw["step"];
		if($review_id = $review->save()) {
			App::model("review_attrs")->append($review_id, $raw["info"]);
			App::model("places")->increase_point_by_place_id($raw["place_id"], $raw);
			App::model("place_attrs")->increase_info_by_place_id($raw["place_id"], $raw["info"]);
			$profile = App::model("profiles")->find_by_account_id($author, true);
			App::model("place_types")->increase_type_by_place_id($raw["place_id"], $profile["type"]);
			return $review_id;
		}
		return false;
    }

/**
 * 
 * @param string 
 * @param integer
 * @param array
 * @param resource
 * @param object
 * @param mix
 * @return
 */
    public function remove($review_id) {
		if($record = $this->find_by_id($review_id)) {
			//データの削除は慎重でなければなりません、トランザクションで保証する
			self::begin();
			App::model("places")->decrease_point_by_place_id($record->place_id, $record->to_array());
			$profile = App::model("profiles")->find_by_account_id($record->author, true);
			App::model("place_types")->increase_type_by_place_id($raw["place_id"], $profile["type"]);
			//レビューや場所の付加情報を更新する
			if($review_attrs = App::model("review_attrs")->find_by_review_id($review_id)) {
				App::model("place_attrs")->decrease_info_by_place_id($record->place_id, $review_attrs->to_array());
				$review_attrs->delete();
			}
			$record->delete();
			self::commit();
		}
    }
	
/**
 * 
 * @param string 
 * @param integer 
 * @param array 
 * @param resource 
 * @param object 
 * @param mix 
 * @return
 */
    public function get_all_by_place_id($place_id, $offset = null) {
		if($offset) {
			$this->limit($offset, 10);
		} else {
			$this->limit(10);
		}
		return $this->find_all_by_place_id($place_id, true);
    }
	
/**
 * 
 * @param string 
 * @param integer 
 * @param array 
 * @param resource 
 * @param object 
 * @param mix 
 * @return
 */
    public function get_all_by_author($author, $offset = null) {
		if($offset) {
			$this->limit($offset, 10);
		} else {
			$this->limit(10);
		}
		return $this->find_all_by_author($author, true);		
    }
	
/**
 * 
 * @param string 
 * @param integer 
 * @param array 
 * @param resource 
 * @param object 
 * @param mix 
 * @return
 */
    public function match_author($review_id, $author) {
		$this->find("author", $author);
		return (bool) $this->find_by_id($review_id);
    }

	
/**
 * 
 * @param string 
 * @param integer 
 * @param array 
 * @param resource 
 * @param object 
 * @param mix 
 * @return
 */
    public function reviews_history($author) {
		//最終レビュー時間が必要ですので、サーバー上でソートする必要がある
		//このデータはクライアント側でキャッシュする必要がありそうだ
		$this->find("author", $author)->group("place_id")->order("register_dt desc");
		return $this->getAll_as_array();
    }
	
}
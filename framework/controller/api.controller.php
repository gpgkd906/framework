<?php
/**
 * api.controller.php
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
 * api_controller
 * 
 * apiはユーザ向けapiです
 *
 * $authorizationがtrueとなっており、全てのアクセスは認証が必要ですので、会員向けのapiを示しています。
 *
 *
 * @author 2014 Chen Han 
 * @package framework.controller
 * @link 
 */
class api_controller extends api {
	/**
	 * 認証が必要かどかの設定
	 * @api
	 * @var boolean
	 * @link
	 */
	public $authorization = true;

/**
 * google mapから取得したplaces情報をサーバー上のデータと照合
 *
 * 存在するれば、サーバー上をデータを統合して返す
 *
 * 存在しなければ、サーバー上に保存する
 *
 * もしサーバーのレスポンスが遅いのであれば、ここが一番疑わしい。
 *
 * 実際パフォーマンスの検証が必要であるが、キャッシュかhandlersocketなどの導入が望ましい
 *
 * handlersocketがパフォーマンス出やすいように設計済み。
 *
 * ../model/places.model.phpのcompare_by_placesメソッドを参照
 * @api
 * @return
 * @link
 */
	public function post_place_id_search() {

		$places = json_decode($this->param["places"], true);

		$result = App::model("places")->compare_by_places($places);

		$place_ids = array_column($result, "place_id");

		$place_attrs = App::model("place_attrs")->find_all_by_place_id($place_ids, true);

		$place_types = App::model("place_types")->find_all_by_place_id($place_ids, true);

		//場所に紐付けた画像も取得
		$place_images = App::model("place_images")->find_all_by_place_id($place_ids, true);

		$file_ids = array_column($place_images, "file_id");

		$files = array_map(function($file) {
				unset($file["file"]);
				return $file;
			}, App::model("files")->find_all_by_id($file_ids, true));

		unset($places);
		unset($place_ids);

		$this->assign(get_defined_vars());
	}

/**
 * 指定したplace_idからサーバー上のデータを取得
 * @api
 * @param String $place_id google map api v3から取得したplace_id
 * @return
 * @link
 */
	public function place($place_id) {

		$place = App::model("places")->find_by_place_id($place_id, true);

		$place_images = App::model("place_images")->find_all_by_place_id($place_id, true);

		$file_ids = array_column($place_images, "file_id");

		$files = array_map(function($file) {
				unset($file["file"]);
				return $file;
			}, App::model("files")->find_all_by_id($file_ids, true));

		$votes = App::model("file_votes")
			->find("voter", App::helper("auth")->id)
			->find_by_file_id($file_ids, true);

		unset($file_ids);

		$this->assign(get_defined_vars());
    }

/**
 * placeの画像情報を投稿する
 * @api
 * @return
 * @link
 */
	public function post_place_image() {

		$author = App::helper("auth")->id;

		$place_id = $this->param["place_id"];

		$dataURL = $this->param["dataURL"];

		$type = isset($this->param["type"]) ? $this->param["type"]: "none";

		$file = App::helper("file")->dataurl_to_file($place_id . microtime(true), $dataURL, config::fetch("upload"));

		$file_id = App::model("files")->create_record($file)->id;

		$vote = 0;

		App::model("place_images")->create_record(array(
				"place_id" => $place_id,
				"author" => $author,
				"file_id" => $file_id,
				"type" => $type,
				"vote" => $vote
		));

		unset($file["file"]);
		unset($place);
		unset($dataURL);

		$this->assign(get_defined_vars());
    }

/**
 * placeに投稿した画像に対して、投票データを追加する
 * @api
 * @param String $place_id Google Map用PlaceId
 * @return
 * @link
 */
	public function put_place_image_vote($place_id) {

		$voter = App::helper("auth")->id;

		$file_id = $this->param["file_id"];

		App::model("file_votes")->create_record(array(
				"file_id" => $file_id,
				"voter" => $voter
		));

		$vote = App::model("place_images")->increase_vote($place_id, $file_id);

		$this->assign(get_defined_vars());
	}

/**
 * placeに投稿した画像に対して、投票データを削除する
 * @api
 * @param String $place_id Google Map用PlaceId
 * @return
 * @link
 */
	public function delete_place_image_vote($place_id) {

		$voter = App::helper("auth")->id;

		$file_id = $this->param["file_id"];

		App::model("file_votes")->find("file_id", $file_id)->find("voter", $voter)->delete();

		$vote = App::model("place_images")->decrease_vote($place_id, $file_id);

		$this->assign(get_defined_vars());
	}

/**
 * placeにレビューを追加する
 * @api
 * @return
 * @link
 */
	public function post_review() {
		$status = false;

		$review = $this->param;

		if($review_id = App::model("reviews")->append($this->param, App::helper("auth")->id)) {
			$status = true;
		}
		$debug = App::model("place_types")->debug;

		$this->assign(get_defined_vars());
	}

/**
 * 指定するレビューを削除する
 * @api
 * @return
 * @link
 */
	public function delete_review() {
		$status = false;

		$review_id = $this->param["review_id"];

		if(App::model("reviews")->match_author($review_id, App::helper("auth")->id)) {
			App::model("reviews")->remove($review_id);
			$status = true;
		}
		$this->assign(get_defined_vars());
	}

/**
 * レビューに対して、「役に立った」を追加する
 * @api
 * @param String $review_id レビューid
 * @return
 * @link
 */
	public function put_review_interesting($review_id) {

		$status = (bool) App::model("interesting")->append(array(
				"review_id" => $review_id,
				"type" => $this->param["type"],
				"sender_id" => App::helper("auth")->id
		));

		$this->assign(get_defined_vars());
	}

/**
 * レビューに対して、「役に立った」を削除する
 * @api
 * @param String $review_id レビューid
 * @return
 * @link
 */
	public function delete_review_interesting($review_id) {

		$status = (bool) App::model("interesting")->remove(array(
				"review_id" => $review_id,
				"type" => $this->param["type"],
				"sender_id" => App::helper("auth")->id
		));

		$this->assign(get_defined_vars());
	}

/**
 * placeに投稿されたレビューを取得する
 * @api
 * @param String $place_id Google Map用PlaceId
 * @param Integer $offset レビュー取得オフセット
 * @return
 * @link
 */
	public function place_reviews($place_id, $offset = null) {

		$reviews = App::model("reviews")->get_all_by_place_id($place_id, $offset);

		$review_ids = array_column($reviews, "id");

		$review_attrs = App::model("review_attrs")->find_all_by_review_id($review_ids, true);

		$review_interestings = App::model("interesting")->find_all_by_review_id($review_ids, true);

		$author_ids = array_column($reviews, "author");

		$authors = App::model("profiles")->find_all_by_account_id($author_ids, true);

		unset($review_ids);
		unset($author_ids);

		$this->assign(get_defined_vars());
	}

/**
 * 自分が投稿したレビューを取得する
 * @api
 * @param Integer $offset レビューのオフセット
 * @return
 * @link
 */
	public function myreview($offset = null) {

		$author = App::helper("auth")->id;

		$reviews = App::model("reviews")->get_all_by_author(App::helper("auth")->id, $offset);

		$review_ids = array_column($reviews, "id");

		$review_attrs = App::model("review_attrs")->find_all_by_review_id($review_ids, true);

		$place_ids = array_column($reviews, "place_id");

		$places = App::model("places")->find_all_by_place_id($place_ids, true);

		$place_images = App::model("place_images")->find_all_by_place_id($place_ids, true);

		$file_ids = array_column($place_images, "file_id");

		$files = array_map(function($file) {
				unset($file["file"]);
				return $file;
			}, App::model("files")->find_all_by_id($file_ids, true));

		$points = App::model("interesting")->find_all_by_review_id($review_ids, true);

		unset($author);
		unset($review_ids);
		unset($place_ids);

		$this->assign(get_defined_vars());
	}

/**
 * 自分が投稿したplaceを取得する
 * @api
 * @return
 * @link
 */
	public function myplace() {

		$group_reviews = App::model("reviews")->reviews_history(App::helper("auth")->id);

		$place_ids = array_column($group_reviews, "place_id");

		$result = App::model("places")->find_all_by_place_id($place_ids, true);

		$place_attrs = App::model("place_attrs")->find_all_by_place_id($place_ids, true);

		//場所に紐付けた画像も取得
		$place_images = App::model("place_images")->find_all_by_place_id($place_ids, true);

		$file_ids = array_column($place_images, "file_id");

		//myplaceの場合はカテゴリも取得が必要です。
		$place_category = App::model("place_category")->find_all_by_place_id($place_ids, true);

		$cate_ids = array_column($place_category, "category");

		$category_name = App::model("category")->find_all_by_id($cate_ids, true);

		$files = array_map(function($file) {
				unset($file["file"]);
				return $file;
			}, App::model("files")->find_all_by_id($file_ids, true));

		unset($place_ids);
		unset($cate_ids);
		$this->assign(get_defined_vars());
	}

/**
 * 自分の統計情報を取得
 * @api
 * @return
 * @link
 */
	public function mystatistics() {

		//投稿数
		$review_count = App::model("reviews")->find("author", App::helper("auth")->id)->count();
		//投稿した画像数
		$image_count = App::model("place_images")->find("author", App::helper("auth")->id)->count();
		//投稿した場所数
		$group_reviews = App::model("reviews")->reviews_history(App::helper("auth")->id);
		$place_ids = array_column($group_reviews, "place_id");

		$place_count = App::model("places")->find("place_id", $place_ids)->count();

		unset($group_reviews);
		unset($place_ids);
		$this->assign(get_defined_vars());
	}


/**
 * プロフィールを取得する
 * @api
 * @param Integer $id アカウントid
 * @return
 * @link
 */
	public function profile($id) {

		$profile = App::model("profiles")->get_one($id);

		$this->assign(get_defined_vars());
	}

/**
 * プロフィールを更新する
 * @api
 * @param Integet $account_id アカウントid
 * @return
 * @link
 */
	public function put_profile($account_id) {
		$status = false;

		if($account_id !== App::helper("auth")->id) {

			$message = "該当アカウントの情報を変更する権限を持っていません。";

		} else {

			$profile = App::model("profiles")->upgrade_by_account_id($account_id, $this->param);

			$status = true;

		}

		$this->assign(get_defined_vars());
	}

}
<?php
/**
 * admin.controller.php
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
 * admin_controller
 * bremenサーバー側管理画面
 *
 * 現状はスポット更新機能を有する。
 *
 * 将来的に各管理機能を追加する予定
 *
 * @author 2014 Chen Han 
 * @package framework.controller
 * @link 
 */
class admin_controller extends application {

	
    /**
	 * 管理画面の権限設定
	 * @api
	 * @return
	 * @link
	 */
    public function before_action () {
		parent::before_action();
		if(!App::helper("auth")->is_valid()) {
			App::redirect("account/login");
		}
		App::helper("auth")->under_admin(function() {
				App::route()->forbidden();
			});
    }

	/**
	 * dashboard画面
	 * 現状はスポット管理しか入ってません
	 * @api
	 * @return
	 * @link
	 */
    public function index () {
		
	}

    /**
	 * スポット一覧・検索・更新処理
	 *
	 *###検索について
	 *
	 * スポット検索要素または更新要素はスポット名、アドレス、電話番号となっております。
	 *
	 * スポット情報に関しては他にカテゴリ情報や都道府県情報などがありますが、
	 * 実際アプリ側の検索ではGoogle Mapの検索を利用しているのでこちらの情報を変更すると、
	 * Google Mapデータとの整合性がとれない可能性もあるので検索も更新も表示ませんように。
	 *
	 * 当バージョンは第一フェーズで、データ収集と考えていただければと思います。
	 *
	 * 次期バージョンでオリジナル地理情報やマップを製作となれば、
	 * 完全なるスポット情報の検索や更新はサポートできるでしょう。
	 * @api
	 * @param   
	 * @param    
	 * @return
	 * @link
	 */
    public function places () {
		$scaffold = App::model("places")->scaffold();
		$this->assign(get_defined_vars());
    }

    /**
	 * レビュー一覧・検索・更新処理
	 *
	 *###検索について
	 *
	 * スポット検索要素または更新要素はスポット名、アドレス、電話番号となっております。
	 *
	 * スポット情報に関しては他にカテゴリ情報や都道府県情報などがありますが、
	 * 実際アプリ側の検索ではGoogle Mapの検索を利用しているのでこちらの情報を変更すると、
	 * Google Mapデータとの整合性がとれない可能性もあるので検索も更新も表示ませんように。
	 *
	 * 当バージョンは第一フェーズで、データ収集と考えていただければと思います。
	 *
	 * 次期バージョンでオリジナル地理情報やマップを製作となれば、
	 * 完全なるスポット情報の検索や更新はサポートできるでしょう。
	 * @api
	 * @param   
	 * @param    
	 * @return
	 * @link
	 */
    public function reviews () {
		$scaffold = App::model("reviews")->scaffold();
		$this->assign(get_defined_vars());
    }

	public function partner() {
		$scaffold = App::model("partners")->scaffold();
		$this->assign(get_defined_vars());		
	}

}
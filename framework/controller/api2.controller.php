<?php
/**
 * api2.controller.php
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
 * api2_controller
 * 
 * $authorizationがfalseである、認証が必要としなく、公開apiを示しています。
 *
 * @author 2014 Chen Han 
 * @package framework.controller
 * @link 
 */
class api2_controller extends api {
	/**
	 * 認証が必要かどか
	 * @api
	 * @var boolean
	 * @link
	 */
	public $authorization = false;

	/**
	 * パートナー情報を取得
	 *
	 * @return
	 * @link
	 */
	public function partners() {
		$status = false;
		if(true) {
			$partners = array("watami");
			$status = true;
		}
		$this->assign(get_defined_vars());
	}
}
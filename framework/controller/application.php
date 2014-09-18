<?php
/**
 * application.php
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
 * application
 * 
 * web applicationコントローラー親クラス
 *
 *
 * @author 2014 Chen Han 
 * @package framework.controller
 * @link 
 */
class application extends controller {

	/**
	 * ルーターインスタンス、古いバージョンとの互換性のため保留
	 *
	 * ルーターインスタンスはurlリダイレクトの時だけ利用する。
	 *
	 * @var
	 * @link
	 */
	protected $route;

	/**
	 * コントローラーが初期化する時一緒に初期化するヘルパー
	 * @api
	 * @var array
	 * @link
	 */
	public $helpers = array(

		"view", "auth",

	);

	/**
	 * アクション前処理
	 *
	 * 各コントローラーの共通処理はbefore_actionで設定できる
	 * @api
	 * @return
	 * @link
	 */
	protected function before_action(){}

	/**
	 * アクション後処理
	 *
	 * 一時データのクリーンアップ処理など
	 * @api
	 * @return
	 * @link
	 */
	protected function after_action(){}

	/**
	 * レンダリング前処理
	 * @api
	 * @return
	 * @link
	 */
	protected function before_render(){}

	/**
	 * レンダリング後処理
	 * @api
	 * @return
	 * @link
	 */
	protected function after_render(){}

}
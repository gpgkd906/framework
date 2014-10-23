<?php
/**
 * base.php
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
 * base_core
 * myFrameworkのbase trait
 *
 * 基本的にフレームワークのコアリソースは全てこのtraitをミクスインしている
 * @author 2014 Chen Han
 * @package framework.core
 * @link
 */
namespace Core;

trait Base {
	/**
	 * メッセージリソースプール
	 *
	 * @var array
	 * @link
	 */
	protected $resources = array();
	/**
	 * 偽装イベントプール
	 *
	 * @var array
	 * @link
	 */
	protected $fake_event = array();

	/**
	 * リソースをオブジェクト間でMessage/Event型で交換する
	 *
	 * リソースをメッセージリソースプールに設定
	 * 
	 * 設定後、監視しているイベントを起こす
	 *@api
	 *
	 * @param String $message メッセージ名
	 * @param Mixed $resource 任意リソース
	 * @return
	 * @link
	 */
	public function send_message($message, $resource = null) {
		$this->resources[$message] = $resource;
		if(isset($this->fake_event[$message])) {
			foreach($this->fake_event[$message] as $callback) {
				if(is_callable($callback)) {
					call_user_func($callback, $resource);
				}
			}
		}
	}

	/**
	 * messageに設定した全てのイベントを解除した上で，新たにイベントを設定し。
	 * @api
	 *
	 * @param String $message メッセージ名
	 * @param Closure $callback イベント処理
	 * @return
	 * @link
	 */
	public function on_message($message, $callback) {
		$this->fake_event[$message] = array( $callback );
	}

	/**
	 * messageにイベントを追加設定する
	 * @api
	 *
	 * @param String $message メッセージ名
	 * @param Closure $callback イベント処理
	 * @return
	 * @link
	 */
	public function add_event_listener($message, $callback) {
		if(empty($this->fake_event[$message])) {
			$this->fake_event[$message] = array();
		}
		$this->fake_event[$message][] = $callback;
	}

	/**
	 * 指定するメッセージ名のリソースを取得
	 * @api
	 *
	 * @param String $message メッセージ名
	 * @return
	 * @link
	 */
	public function read_message($message) {
		if(isset($message)) {
			return $this->resources[$message];
		}
	}

}
<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class controller_checkauth extends base_controller
{
	private $model_comment;
	private $model_reg;
	private static $_token = 'adoxiao2abc1124a';

	public function __construct() {
		parent::__construct();
	}
	public function AtIndex() {
		$echoStr = DF::app()->request->params('echostr');
		if($this->checkSignature()){
			echo $echoStr;
			exit;
		}
		echo 'fail';
	}
	/*--home--*/
	private function checkSignature() {
		$signature = DF::app()->request->params('signature');
		$timestamp = DF::app()->request->params('timestamp');
		$nonce = DF::app()->request->params('nonce');
				
		$token = self::$_token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}
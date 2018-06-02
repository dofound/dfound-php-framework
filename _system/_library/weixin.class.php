<?php
/**
 * @author xiaojh@
 * @date 11:11 2015/3/20
 * use:
 * 
 * 微信应用
 * @
 *   
 */
define("_TOKEN", "adoxiao2abc1124a");
define('_APPID','wx0cdc6c03460b9ec2');
define('_SECRET','dce8370593556d51af487e1357ba8876');
define('OPENID','');
class lib_weixin
{
	private static $_memcache;
	public function __construct() {
		if (empty($_GET['bdebug']) && !$this->isFromWeiXin()) die('Sorry,please from weixin!!');
		if (empty(self::$_memcache)) {
			self::$_memcache = memcache_init();
		}
	}
	public function loginMsg() {
		$obj = new weixin(self::$_memcache);
		$access = $obj->login();
		if (empty($access)) die('sorry.');
		echo 'ok';
		//$obj->responseMsg();
	}
	public function putMsg($data) {
		$obj = new weixin(self::$_memcache);
		$obj->responseMsg($data,'text');
	}

	public function valid() {
		$echoStr = DF::app()->request->params('echostr');
		if($this->checkSignature()){
			echo $echoStr;
			exit;
		}
		echo 'fail';
	}
	private function isFromWeiXin() {
		$echoStr = DF::app()->request->params('echostr');
		if($this->checkSignature()) return true;
		return false;
	}
	/*--home--*/
	private function checkSignature() {
		$signature = DF::app()->request->params('signature');
		$timestamp = DF::app()->request->params('timestamp');
		$nonce = DF::app()->request->params('nonce');
				
		$token = _TOKEN;
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

	/**
	 * 将临时变量置空
	 */
	public function __destruct() {	}
}

//
class weixin 
{
	//access api
	private $_accessUrl = 'https://api.weixin.qq.com/cgi-bin/token';
	private $_access_key = 'access_key_info_v1';
	private $_cacheTime = 7190;
	private static $_mem;
	private static $_accessMem;

	public function __construct($_mem) {
		self::$_mem = $_mem;
	}

	//基础支持-获取access_token
	public function login() {
		if (!self::$_mem) die('Memcache is dropped.');
		$access = self::$_mem->get($this->_access_key);
		if (!empty($access)) { return $access;}

		$params = array(
			'grant_type'=>'client_credential',
			'appid'=>_APPID,
			'secret'=>_SECRET,
		);
		$access = lib_fsocket::curlGetSsl($this->_accessUrl,$params,1);
		$access = json_decode($access,true);
		if (empty($access['access_token'])) { die('sorry,no access_token');}
		self::$_mem->set($this->_access_key, $access['access_token'], 0,$this->_cacheTime);

		return $access['access_token'];
	}

	//基础支持-获取微信服务器IP地址
	public function ip() {


	}
	//接收消息-验证消息真实性、接收普通消息、接收事件推送、接收语音识别结果
	public function recvMsg() {


	}
	//发送消息-被动回复消息
	public function sentMsg() {


	}
	public function responseMsg($data,$msgType='text') {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

		//extract post data
		if (!empty($postStr)){
				/* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
				   the best way is to check the validity of xml by yourself */
				libxml_disable_entity_loader(true);
				$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
				$fromUsername = $postObj->FromUserName;
				$toUsername = $postObj->ToUserName;
				$keyword = trim($postObj->Content);
				$msgId = $postOjb->MsgId;
				$time = time();
				$textTpl = $this->msgTpl($msgType);
				if (!empty( $keyword )) {
					switch ($msgType) {
						case 'text':
							echo $this->tplText($textTpl,$data,$fromUsername, $toUsername, $time, $msgType,$msgId);
							break;
						case 'image':
							$data = array('http://m1.sinaimg.cn/maxwidth.360/m1.sinaimg.cn/73878916ebd7cea17874d0b68be2ff08_950_1356.jpg','http://m1.sinaimg.cn/maxwidth.360/m1.sinaimg.cn/2f3227cc4e4ab3b89bcaded9e09fd9ac_950_1425.jpg');
							echo $this->tplImage($textTpl,$data,$fromUsername, $toUsername, $time, $msgType,'123',$msgId);
							break;
						case 'link':
							$data = array(
								array('title'=>'名花倾国两相欢','desc'=>'good','link'=>'http://dfound.sinaapp.com')
							);
							echo $this->tplLink($textTpl,$data,$fromUsername, $toUsername, $time, $msgType,$msgId);
							break;
					}
				}else{
					echo "Input something...";
				}

		}else {
			echo "";
			exit;
		}
	}
	private function setMsg($access,$touser) {
		$sent_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';
		$infos = array(
			'touser'=>$touser,
			'msgtype'=>'text',
			'text'=>array('content'=>"hello,my name is xiaojianhe,what's your name?"),
		);
		$params = array('access_token'=>$access,'body'=>json_encode($infos));
		lib_fsocket::curlGetSsl($sent_url,$params);
	}
	private function tplText($textTpl,$data,$fromUsername, $toUsername, $time, $msgType,$msgId) {
		$contentStr = $data[mt_rand(0,count($data)-1)];
		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr,$msgId);
		return $resultStr;
	}
	private function tplImage($textTpl,$data,$fromUsername, $toUsername, $time, $msgType,$mediaId,$msgId) {
		$contentStr = $data[mt_rand(0,count($data)-1)];
		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr,$mediaId,$msgId);
		return $resultStr;
	}
	private function tplLink($textTpl,$data,$fromUsername, $toUsername, $time, $msgType,$msgId) {
		$contentStr = $data[mt_rand(0,count($data)-1)];
		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr['title'],$contentStr['desc'],$contentStr['url'],$msgId);
		return $resultStr;
	}
	private function msgTpl($type) {
		$type = trim($type);
		$tpl = '';
		switch($type) {
			case 'text':
				$textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<MsgId>%s</MsgId>
				<FuncFlag>0</FuncFlag>
				</xml>";
				break;
			case 'image':
				$textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<PicUrl><![CDATA[%s]]></PicUrl>
				<MediaId><![CDATA[%s]]></MediaId>
				<MsgId>%s</MsgId>
				</xml>";
				break;
			case 'link':
				$textTpt = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Title><![CDATA[%s]]></Title>
				<Description><![CDATA[%s]]></Description>
				<Url><![CDATA[%s]]></Url>
				<MsgId>%s</MsgId>
				</xml>";
				break;
		}
		return $textTpl;
	}
	//获取素材
	public function resInfos($access) {
		$api = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$access;
		$params = array(
			'type'=>'news',
			'offset'=>0,
			'count'=>5,
		);
		$access = lib_fsocket::curlGetSsl($api,$params,1);
		print_r($access);
	}
}

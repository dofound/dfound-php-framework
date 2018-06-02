<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class controller_index extends base_controller
{
	private $model_comment;
	private $model_reg;
	private static $_token = 'adoxiao2abc1124a';

	public function __construct() {
		parent::__construct();
	}
	/*--home--*/
	public function AtIndex() {
		$infodata = array(
		"My name is xiaojianhe,Thanks,what's your name?",
		'Haha,my baby',
		'你.想我吗？',
		'你.来爱我吧',
		'你.找MM么？',
		);
		$object = new lib_weixin();
		$object->putMsg($infodata);
	}
	/*--frame--*/
	public function AtFrame() {
		$data = array(1,2,3,4);
		$this->pageTitle = 'Welcome to DoFound PHP Framework.';
		$this->setTpl( 'main',compact('data') );
	}

}
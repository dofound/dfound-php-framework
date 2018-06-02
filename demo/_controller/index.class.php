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
	public function __construct() {
		parent::__construct();
        //$this->model_reg = new model_reg();
        //$this->model_comment = new model_comment();
	}
	/*--home--*/
	public function AtIndex() {
		$data = array(1,2,3,4);
		$this->pageTitle = 'Welcome to DoFound PHP Framework.';
		$this->setTpl( 'main',compact('data') );
	}
	/*--test--*/
	public function AtTest() {
	   echo DF::app()->request->get('ab');echo '_';
	   echo DF::app()->request->get('cd');
	}
	public function AtGetuser() {
		$uid = DF::app()->request->get('uid');
		$info = $this->model_reg->get_record_one("`id`='{$uid}'");
		print_r($info);
	}
	public function AtGetcomment() {
		$id = DF::app()->request->get('id');
		$info = $this->model_comment->get_record_id($id);
		print_r($info);
	}
}
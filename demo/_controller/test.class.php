<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class controller_test extends base_controller
{
	private $model_comment;
	private $model_reg;

	public function __construct() {
		parent::__construct();
		$this->layout = 'test';
	}
	/*--home--*/
	public function AtIndex() {
		$data = array(1,2,3,4);
		$this->pageTitle = 'test';
		$this->setTpl( 'test',compact('data') );
	}
	/*--test--*/
	public function AtTest() {
	   echo DF::app()->request->get('ab');echo '_';
	   echo DF::app()->request->get('cd');
	}
}
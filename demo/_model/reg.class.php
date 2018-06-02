<?php
/*====================================================*
*	index.php?site=&mod=xx&act=xx	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class model_reg extends base_Mbase
{
	private static $db;
	public function __construct( $db_key = 'db' ) {
		if (empty(self::$db))
			self::$db = new data_reg($db_key);
		parent::__construct( self::$db );
	}

	/**
	 * @param $userInfos
	 * @param uid,name,email,type
	 * */
	public function addSession($userInfos) {
		DF::app()->session->add('user',
			array('uid'=>$userInfos['uid'],'name'=>$userInfos['name'],
			'email'=>$userInfos['email'],'type'=>$userInfos['type'])
		);
		return true;	
	}
	/**
	 * get session
	 * @return
	 * 
	 * */
	public function getSession() {
		return DF::app()->session->get('user');
	}
	/**
	 * get session and if login in  
	 * @return
	 * 
	 * */
	public static function ifLogin() {
		$uinfo = DF::app()->session->get('user');
		if (empty($uinfo)) return false;
		return $uinfo;
	}
}
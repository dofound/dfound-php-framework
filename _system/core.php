<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
define('_VER','0.1');
define('_DOMAIN',$_SERVER['HTTP_HOST']);
define("BASE_URL", 'http://'._DOMAIN.'/');
define('_WWW',substr(_DOMAIN,0,strpos(_DOMAIN, '.')));
define('_PJ',_APP_FOLDER);	//project args
define('ITEM_DIR',_PATH.'/'._PJ);
define("CTRL_DIR", ITEM_DIR . "/_controller/");
define("MOD_DIR", ITEM_DIR . "/_model/");
define('TEMPLATE_DIR', ITEM_DIR . '/_templates/'); 
define('DATA_DIR', ITEM_DIR . '/_data/');
define('SOURCE_DIR', ITEM_DIR . '/resource/');			// source file
define('SYSTEM_DIR', _PATH . _SYSTEM.'/_library/');		//library file 
define('CONFIG_DIR', _PATH . _SYSTEM.'/_config/');		//config file
define('FUNCTION_DIR', _PATH . _SYSTEM.'/_function/');	//function file 
/*--- new throw exception message --*/
class myThrow extends Exception
{
	private $_isReturn = 0;

	public function __construct( $mess='' ) {
		if (_STATUS=='online') {
			$this->return_url();		
		} else {
			$this->_is_return();
			$this->_isReturn ? parent::__construct( $mess ) : '';
		}
	}
	//show all message
	public function all_message() {
		if ( $this-> _isReturn ) {
			return "exception '".__CLASS__ ."' with message '".$this->getMessage()."' in ".$this->getFile().":".$this->getLine()."\nStack trace:\n<br /><br />".$this->getTraceAsString();
		} else {
			$this->return_url();
		}
	}
	//show part message
	public function part_message() {
		if ( $this-> _isReturn ) {
			return " with message '".$this->getMessage()."': at line ".$this->getLine();
		} else {
			$this->return_url();
		}
	}
	//return is 0 or 1
	private function _is_return() {
		$this->_isReturn = _STATUS!='online' ? 1 : 0;
	}
	//return homepage
	private function return_url() {
		header( 'Location:/' );exit;
	}
}
spl_autoload_register(array('DF','autoload'));
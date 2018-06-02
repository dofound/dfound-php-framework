<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
abstract class base_session
{
    /**
     * @author xiaojh
     * get session id
     * */
	protected function sid() {
		return session_id();
	}
    /**
     * @author xiaojh
     * get session max lifeTime
     * */
	protected function getTimeout() {
		return (int)ini_get('session.gc_maxlifetime');
	}
    /**
     * @author xiaojh@
     * set session max lifeTime
     * */
	protected function setTimeout($value) {
		ini_set('session.gc_maxlifetime',$value);
	}
    /**
     * get save path
     * 
     * */
	protected function getSavePath() {
		return session_save_path();
	}
	/**
     * the current session save path
	 * @param string $value
	 * 
	 */
	protected function setSavePath($value) {
		if(is_dir($value))
			session_save_path($value);
	}
    /**
     * @author xiaojh
     * add session
     * */
	public function add($key,$value) {
		$_SESSION[$key]=$value;
	}
    /**
     * @author xiaojh
     * get session
     * */
	public function get($key,$default = NULL) {
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
	}
    /**
     * @author xiaojh
     * delete session
     * */
	public function del($name) {
		unset($_SESSION[$name]);
	}
    /**
     * @author xiaojh
     * delete all
     * */
	public function out() {
		session_destroy();
	}
}
<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class base_memSession extends base_session
{
    /** true is memcahced or memcache **/
    public $is_memCached = FALSE;
    private static $memCache = NULL;
    private $preKey = 'sysMem_';
    /** cookie domain **/
    private $domain;
    /** cookie life **/
    public $expire = 0;    
    /** $ifset **/
    private static $ifset = FALSE;
    /** $if load session_start **/
    private static $isauto = true;
    /**
     * construct
     * apply session
     * #ini_set('session.name', 'dofoundsession');
     * */
    public function __construct(&$memCache) {
        if (self::$memCache===NULL) {
            self::$memCache = $memCache;
        }
        $this->open();
		$this->domain = DF::params('domain');
    }
    /**
     * get memcache
     * */
    private function getMemCache() {
        return self::$memCache;
    }
    /**
     * @author xiaojh
     * open inition session
     * 
     * */
    public function open() {
        if (self::$isauto) {
            session_set_save_handler(
    			array($this,"ses_open"), 
                array($this,"ses_close"), 
                array($this,"ses_read"),
                array($this,"ses_write"), 
                array($this,"ses_destroy"), 
                array($this,"ses_gc")
    		);    
            self::$isauto = false;
            #ini_set('session.name', 'dofoundsession');
        }
        session_start();
    }
    /**
     * open session
     * */
    public function ses_open($key){
        $this->ifSetCookie($key);
        return true;
    }
    /**
     * @author xiaojh
     * read session
     * */
    public function ses_read($key) {
        $rs = $this->getMemCache()->get($this->preKey.$key);
        return $rs;
    }
    /**
     * @author xiaojh
     * write session
     * 
     * */
    public function ses_write($key,$value) {
        $ses_life = $this->getTimeout();       
        $this->getMemCache()->set($this->preKey.$key,$value,$ses_life);      
    }
    /**
     * @author xiaojh
     * close session
     * */       
    public function ses_close() {
        return true;
    }
    /**
     * @author xiaojh
     * destroy session
     * 
     * */
    public function ses_destroy($key) {
        return $this->getMemCache()->del($key);
    }
    /**
     * @author xiaojh
     * gc session
     * */
    public function ses_gc() {
        return true;
    }
    /**
     * @author xiaojh
     * remeber user information
     * */  
    private function _remeber() {
        $this->expire = time() + 86400 * 30;
        $this->setTimeout(86400 * 30);
        $_SESSION['remeberPass'] = 1;   
    }
    /**
     * @author xiaojh@
     * if set remeber password cookies
     * */
    private function ifSetCookie($id) {
        if (self::$ifset === false) {
            self::$ifset = true;
            if(isset($_SESSION['remeberPass']) && $_SESSION['remeberPass'] == 1) {
                $this->_remeber();
            }
            setcookie(session_name(), $this->sid(), $this->expire, '/', $this->domain);
        }
    }
}
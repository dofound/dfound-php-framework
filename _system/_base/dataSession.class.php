<?php
/**
 * /_class/session.class.php
 * 
 * <p>session жиди </p>
 * 
 * @author Xiaojh@
 * @history 11:54 2012-12-12
 *
 *
	CREATE TABLE IF NOT EXISTS `user_session` (
	  `id` varchar(32) NOT NULL,
	  `uid` int(11) NOT NULL,
	  `sdatas` text NOT NULL,
	  `start_time` int(11) NOT NULL,
	  `ip` varchar(16) NOT NULL,
	  `active_time` int(11) NOT NULL,
	  UNIQUE KEY `id` (`id`),
	  KEY `active_time` (`active_time`),
	  KEY `uid` (`uid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='user session';

	CREATE TABLE IF NOT EXISTS `user_session` (
	  `id` char(32) NOT NULL,
	  `uid` int(11) NOT NULL,
	  `sdatas` text NOT NULL,
	  `start_time` int(11) NOT NULL,
	  `ip` varchar(16) NOT NULL,
	  `active_time` int(11) NOT NULL,
	  UNIQUE KEY `id` (`id`),
	  KEY `uid` (`uid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='user session'; 

 *
 *
 */
class base_dataSession extends base_session
{ 

	public $lifeTime = 1000;	// life time min
	protected $db;			    // db handle
    private static $_db;
    /** $if load session_start **/
    private static $isauto = true;
    
	/*---session construct---*/
	public function __construct() {
		$this->lifeTime = empty($this->lifeTime) ? @get_cfg_var("session.gc_maxlifetime") : $this->lifeTime; 
		if (empty(self::$_db)) {
            $this->db = self::$_db = new base_Msession();
        }
        $this->open();
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
	/*---session open---*/
	public function ses_open() {
		return true;
	}
	/*---session need read---*/
	public function ses_read($sessID) {
        $sdata = $this->db->get_record_id($sessID);
        if ( !empty($sdata['info']) ) {
            return $sdata['info']['sdatas'];
        }
        return false;        
	}
	/*---session need write---*/
	public function ses_write($sessID,$sdatas) {
		$res = $this->db->get_record_field("`id`='".$sessID."'");
		if( !empty($res['info']) ) {	//if exists sid
			$tmp_arr = array(
				'uid'=>(int)@$_SESSION['uid'],
 				'ip'=>$_SERVER['REMOTE_ADDR'],
                'sdatas'=>$sdatas,
				'active_time'=>time(),
			);
			$this->db->edit_record_condition($tmp_arr,"`id`='".$sessID."'");		
		} else {
			$tmp_arr = array(
				'id'=>$sessID,
				'uid'=>0,
				'ip'=>$_SERVER['REMOTE_ADDR'],
				'start_time'=>time(),
                'sdatas' => $sdatas,
				'active_time'=>time(),
			);
			$this->db->add_record($tmp_arr);
		}
		return true;
	}
	/*---session clear/close---*/
	public function ses_close() { 
		return true;
	}
	/*---session clear/close---*/
	public function ses_gc() { 
		$mpt=time();
		$new_times = abs($mpt-$this->lifeTime);
		$lost_users = $this->db->get_record_all("`active_time`<=".$new_times);
		@reset($lost_users);
		while ( list(,$ud)=@each($lost_users) ) {
			$duid=$ud['uid'];
			$ssid=addslashes($ud['id']);
			if (empty($duid)) {
				$this->db->del_record_id($ssid);
			}
		}
		return true;
	}

	/*---session need destroy---*/
	public function ses_destroy($sessID) { 
		$this->db->del_record_id($sessID); 
		return true;
	}
    
}

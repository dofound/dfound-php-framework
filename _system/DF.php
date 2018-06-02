<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class DF 
{
	private static $_config;
	private static $_db;
	private static $_app;
	public $request;
	public $memCache;
    public $redis;
	public $session;
	//public static $_debug = 0;
	public function __construct() {}
	private final function _init() {
		$this->loadConfig();
		$this->setLibrary();
		//self::$_debug++;
		$this->request = new base_request();
	}
	/**
	 * @author xiaojh@
	 * set app()
	 * */
	public static function app() {
		if ( empty(self::$_app) ) {
			self::$_app= new self;
			self::$_app->_init();
		}
		return self::$_app;
	}
	/**
	 * @author xiaojh
	 * load config information
	 * */
	private function loadConfig() {
		self::$_config = !empty(self::$_config) ? self::$_config : self::load(CONFIG_DIR.'/config.class.php',1);
	}
	/**
	 * @author xiaojh
	 * get config information
	 * */
	public static function config($pkey='') {
		return self::$_config[$pkey];
	}
	public static function params($pkey) {
		return self::$_config['params'][$pkey];
	}
	/**
	 * @author xiaojh
	 * 
	 * */
	private function ifVar($name) {
		return in_array($name,array('session','memCache','request','_config','_request','_db','_session'));
	}
	public function __set($name, $value) {
		if ( !$this->ifVar($name) ) {
			$this->$name = $value;
		}
	}
	public function __get($name) {
		if ( !$this->ifVar($name) ) {
			if ( $name==='session' ) {
				
			} else if ( array_key_exists($name, self::$_config) ) {
				return self::$_config[$name];
			}
		}
	}
	/**
	 * @author xiaojh
	 * connect db
	 * */
	private static function connectDb($db_key) {
		self::$_db[$db_key] = !empty(self::$_db[$db_key]) ? self::$_db[$db_key] : new base_Ldatabase( self::$_config['database'][$db_key]);
	}
	/**
	 * @author xiaojh@
	 * @param $db_key from config set
	 * @return object;
	 * */
	public static function getDb($db_key) {
		self::connectDb($db_key);
		return self::$_db[$db_key];
	}
	/**
	 * @author xiaojh@
	 * @param $path
	 * @param $return is return 
	 * 
	 * */    
	public static function load($path,$return=0) {
		if ($return) {
			return require_once $path;
		}
		require_once($path);
	}
	/**
	 * some important class files
	 * @author xiaojh@
	 * 
	 * */
	public static function loadSys() {
		$class = array('core.php','dofound.php','_base/controller.class.php','_base/request.class.php');
		foreach ( $class as $pv ) {
			self::load(_PATH._SYSTEM.'/'.$pv);
		}
	}
	/**
	 * @author xiaojh
	 * 
	 * some other library information
	 * exmple:
	 * 
	 * */
	public function setLibrary() {
        if (!empty(self::$_config['memcache'])) {
            $this->memCache = new base_memCache(self::$_config['memcache']);
        }
        if (!empty(self::$_config['redis'])) {
            $this->redis = base_redisCache::connect(self::$_config['redis'][0]);
        }        
		if (_SESSION=='database') {
			$this->session = new base_dataSession();
		} else if (_SESSION=='memcache') {
            $this->session = new base_memSession($this->memCache);
		} else {
            $this->session = new base_sysSession();		  
		}
	}
	/**
	 * autoload class 
	 * @author xiaojh@
	 * */
	public static function autoload( $classname ) {
		$path = explode('_',$classname);
		$file = '';
		switch ($path[0]) {
			case 'model':
				$classname = str_replace( 'model_', '', $classname );
				$classname = str_replace( '_', '/', $classname );
				$file = MOD_DIR . $classname . '.class.php';
				break;
			case 'modules':
				$classname = str_replace( 'modules_', '', $classname );
				$classname = str_replace( '_', '/', $classname );
				$file = MOD_DIR . $classname . '.class.php';
				break;            
			case 'controller':
				$classname = str_replace( 'controller_', '', $classname );
				$classname = str_replace( '_', '/', $classname );
				$file  = CTRL_DIR . $classname . '.class.php';
				break;
			case 'data':
				$classname = str_replace( 'data_', '', $classname );
				$classname = str_replace( '_', '/', $classname );
				$file  = DATA_DIR . $classname . '.class.php';
				break;
			case 'config':
				$classname = str_replace( 'config_', '', $classname );
				$classname = str_replace( '_', '/', $classname );
				$file = CONFIG_DIR.$classname.'.class.php';
				break;
			case 'lib':
				$classname = str_replace( 'lib_', '', $classname );
				$classname = str_replace( '_', '/', $classname );
				$file = SYSTEM_DIR.$classname.'.class.php';
				break;
			case 'fun':
				$classname = str_replace( 'fun_', '', $classname );
				$classname = str_replace( '_', '/', $classname );
				$classname = empty($classname) ? 'fun' : $classname;
				$file = FUNCTION_DIR.$classname.'.class.php';
				break;
			case 'base':
				$classname = str_replace( 'base_', '', $classname );
				$fist = $classname{0};
				if ($fist=='D')	{
					$classname = 'data/'.substr($classname,1);
				} else if($fist=='M') {
					$classname = 'model/'.substr($classname,1);
				} else if($fist=='L') {
					$classname = 'db/'.substr($classname,1);
				}
				$file = _PATH._SYSTEM.'/_base/'.$classname.'.class.php';
				break;
			default:
				$classname = str_replace( '_', '/', $classname );
				$file = ITEM_DIR.'/'.$classname.'.class.php';
			/*	$file = ITEM_DIR.'/library/'.$classname.'.class.php';
			*/
				break;
		}
		if ( file_exists($file) ) {
			self::load($file);
		} else {
			throw new myThrow( $file.' file no exists' );
		}
		return true;
	}
}
DF::loadSys();
<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
 class base_memCache
 {
	protected $memcache = null;
	protected static $is_memCached = false;
	protected static $is_compressed = false;
	private $preKey = 'sysMem_';
	/**
	 * construct
	 * apply memcache
	 * @param $addServer array() 
	 * $servers = array(
			array('mem1.domain.com', 11211, 33),
			array('mem2.domain.com', 11211, 67)
		);
	 *
	 * @param $is_memcached boolen
	 * 
	 * */
	public function __construct($addSevers=array(),$is_memCached=false) {
		if (empty($addSevers)) {
			$addSevers = array(
				array('127.0.0.1',11211,100)
			);
		}
		$this->connect($addSevers,$is_memCached); 
	}
	/**
	 * @author xiaojh
	 * connect memcache
	 * */
	public function connect($addServers,$is_memCached) {
		$pstatus = false;
		if ( $is_memCached ) {
			$this->memcache = new Memcached();
			$this->memcache->addServers($addServers);
		} else {
			$this->memcache = new Memcache();
			foreach ($addServers as $mc) {
				$this->memcache->addServer($mc[0],$mc[1],$mc[2]);
			}
		}
		self::$is_memCached = $is_memCached;
	}
	/**
	 * @author xiaojh
	 * add new key information
	 * */
	public function add($key,$value,$expire=0) {
		if ($expire>0)	$expire+=time();
		$this->memcache->add($key,$value,self::$is_compressed ? MEMCACHE_COMPRESSED:0,$expire);        
	}
	/**
	 * @author xiaojh
	 * set information
	 * 
	 * */
	public function set($key,$value,$expire=0) {
		if ($expire>0)	$expire+=time();
		$this->memcache->set($key,$value,self::$is_compressed ? MEMCACHE_COMPRESSED:0,$expire);        
	}
    public function increment($key,$value) {
        $this->memcache->increment($key,$value);
    }
    public function decrement($key,$value) {
        $this->memcache->decrement($key,$value);
    }
	/**
	 * @author xiaojh
	 * read session
	 * */
	public function get($key) {
		return $this->memcache->get($key);
	}
	/**
	 * @author xiaojh
	 * del session
	 * @return resource
	 * */       
	public function del($key,$timeout=0) {
		return $this->memcache->delete($key,$timeout);
	}
	/**
	 * flush memcahce
	 * */
	public function flush() {
		return $this->memcache->flush();
	}
	/**
	 * close memcache
	 * */
	public function close() {
		return $this->memcache->close();
	}
	/**
	 * Memcached normal
	 * 
	 * @author xiaojh@
	 * @param $key
	 * 
	 * @return array
	 * */
	public function getMulti($key) {
		if (!self::$is_memCached) return false;
		return $this->memcache->getMulti($key);
	}
 }

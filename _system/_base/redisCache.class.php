<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
 class base_redisCache
 {
	protected static $redis = null;
	/**
	 * @author xiaojh
	 * connect memcache
	 * */
	public function connect($addServers) {
		if (self::$redis==null) {
			$redis = new redis();
			$redis->connect($addServers[0],$addServers[1]);
			self::$redis = $redis;
		}
		return self::$redis;
	}
 }

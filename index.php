<?php
/**
* @value: if gzip.
*/
@ob_start('ob_gzhandler') || ob_start();
//ini_set('display_errors',1);
/**
* @value: set local time.
*/
date_default_timezone_set('Asia/Shanghai');
/**
* @value: start time.
*/
define('_TIME', microtime());

/**
* @value: test && dev | online
*/
define('_STATUS', 'dev');

/**
* @value: true is all infos | false is part infos
*/
define('_DEBUG',false);

/**
* @value:core system filename
*/
define('_SYSTEM','_system');

/**
* @value:application_folder
*/
define('_APP_FOLDER','demo');

/**
* @value:current file path
*/
define('_CURRENT',__FILE__);

/**
* @value:current file name
*/
define('_SELF', pathinfo(_CURRENT, PATHINFO_BASENAME));

/**
* @value:core system path
*/
define('_PATH', str_replace(_SELF,'',strtr(_CURRENT,"\\", "/")));

/**
* @value:cache path
*/
define('_CACHE', _PATH.'appdata/');

/**
* @value:uri route rules 0/1/2 { three levels }
*
* for example:
* 0:http://www.dofound.net/?mod=index&act=test&ab=1
* 2:http://www.dofound.net/index/test/ab/1
* 1:http://www.dofound.net/index/test?ab=1
*/
define('_ROUTE', 0);
/**
* @value: _ROUTE==2 or 1 && url suffix 
*/
define('_EXT', '.html');

/**
* @value: database/memcache/system..wait..
* setting the session key ring
*/
define('_SESSION','system');

/*-- application core --*/
require_once _PATH._SYSTEM.'/DF.php';

/*====================================================*
*	index.php?site=xxx&mod=xx&act=xx	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/

<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
_STATUS == 'dev' ? error_reporting(E_ALL ^ E_NOTICE) : error_reporting(0);
DF::app()->request->appRoute();
if ( !empty($GLOBALS['site']) ) {
	define('_SITE',$GLOBALS['site']);
} else {
	if (_WWW!='dfound') {
		define('_SITE',_WWW!='www' ? _WWW : '');
	} else {
		define('_SITE','');
	}
}
/*--- sailor ---*/
try {
	$mod = _SITE ? _SITE.'_controller_'.$GLOBALS['mod'] : 'controller_'.$GLOBALS['mod'];
	$class = new $mod();
    $ESCaCT = 'At'.ucfirst($GLOBALS['act']);    
    if ( in_array($ESCaCT,get_class_methods($mod)) ) {
		$class->{$ESCaCT}();
    } else {
		throw new myThrow();
	}
} catch( myThrow $e ) {
	echo _DEBUG ? $e->all_message() : $e->part_message();
}
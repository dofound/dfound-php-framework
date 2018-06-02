<?php
/*=================================
*	Author:xiaojh@
*	Time:2013-6-29 20:23
*	Fun:home
*=================================*/
class model_comment extends base_Mbase
{
	private static $db;
	public function __construct( $db_key = 'db' ) {
		if (empty(self::$db))
			self::$db = new data_comment($db_key);
		parent::__construct( self::$db );
	}
}
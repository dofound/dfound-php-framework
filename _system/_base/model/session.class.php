<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class base_Msession extends base_Mbase
{
	public function __construct( $db_key = 'db' ) {
		parent::__construct( new base_Dsession($db_key) );
	}
}
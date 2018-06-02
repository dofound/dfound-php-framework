<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class base_Dsession extends base_Dbase
{
	public function __construct( $db_key ) {
		parent::__construct( $db_key );
		$this->_table = $this->_table_prefix.'user_session';
	}
}
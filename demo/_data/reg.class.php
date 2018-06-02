<?php
/*=================================
*	Author:xiaojh@
*	Time:2013-6-29 20:22
*	Fun:home

CREATE TABLE `user_reg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL,
  `password` char(32) NOT NULL,
  `email` char(30) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 

*=================================*/
class data_reg extends base_Dbase
{
	public function __construct( $db_key,$db_wkey=null ) {
		parent::__construct( $db_key,$db_wkey );
		$this->_table = $this->_table_prefix.'user_reg';
	}
}
<?php
/*=================================
*	Author:xiaojh@
*	Time:2013-6-29 20:22
*	Fun:home
*

CREATE TABLE IF NOT EXISTS `user_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:main,1:sub',
  `type` tinyint(1) unsigned NOT NULL COMMENT 'type',
  `contents` text NOT NULL,
  `datelime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

*
*
*=================================*/
class data_comment extends base_Dbase
{
	public function __construct( $db_key ) {
		parent::__construct( $db_key );
		$this->_table = $this->_table_prefix.'user_comment';
	}
}
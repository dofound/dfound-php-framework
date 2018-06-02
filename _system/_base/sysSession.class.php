<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class base_sysSession extends base_session
{
    /**
     * construct
     * apply session
     * #ini_set('session.name', 'dofoundsession');
     * */
    public function __construct() {
        $this->open();
    }
    /**
     * @author xiaojh
     * open inition session
     * 
     * */
    public function open() {
        session_start();
    }

}
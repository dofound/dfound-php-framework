<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
abstract class base_controller
{
	protected $_DATA = array();
	protected $_TPL;
    public $pageTitle;
    
	public $layout = 'main';

	public function __construct() {}

	/*-- set --*/
	public function __set( $name,$value ) {
		$this->$name = $value;
	}
	/*-- get --*/
	/*public final function __get( $name ) {
		return $this->_DATA[$name];
	}*/
	/*-- private views --*/
	private function __views() {
		!empty($this->_DATA) ? @extract($this->_DATA,EXTR_PREFIX_SAME,'do') : '';
		$DfInclude = TEMPLATE_DIR.$this->_TPL.'.html';
		$DfMemory = $this->showMemory();
		include_once TEMPLATE_DIR.'../layout/'.$this->layout.'.php';
	}

	/**
     * protected show
     * @param string $tpl	- template file
	 * @param array $data	- data records
     * @return null
     */
	protected final function setTpl( $tpl,$data='' ) {
		$this->_TPL		= _SITE ? _SITE.'/'.$tpl : $tpl;
		$this->_DATA	= $data;
	}

	/**
	 * protected __get_methods
	 * @param string $classname	- class name
	 * @return array
	 */
	protected function __get_methods( $classname ) {
		$class_methods = get_class_methods( $classname );
		$methods = array();
		foreach($class_methods as $method_name) {
			if($method_name{0}=='_') continue;
			$methods[] = $method_name;
		}
		return $methods;
	}
    /**
     * get www path
     * @param $url
     * */
    public function getUrl($url) {
        return DF::app()->request->setUrl( 'www',$url );
    }    
    /**
     * get css path
     * @param $url
     * */
    public function getStyle($url) {
        return DF::app()->request->setUrl( 'style',$url );
    }
    /**
     * get resources path
     * @param url
     * */
    public function getCommon($url) {
        return DF::app()->request->setUrl( 'common',$url );
    }
    /**
     * get include path
     * @param url
     * */
    public function getInclude($url) {
        include_once(DF::app()->request->setUrl( 'include',$url ));
    }
    /**
     * @author xiaojh@
     * get file bytes
     * */
    protected function getSize( $size ) { 
        $unit=array('b','kb','mb','gb','tb','pb'); 
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
    }
    private function debug() {
        if (_DEBUG) {
			base_Ldatabase::getBug($this->_DATA);
		}
        unset($this->_DATA);
    }
    /**
     * @param $mtime microtime
     * */
    private function microtimeFloat($mtime) {
        list($msec, $sec) = explode(' ', $mtime);
        return (double)$msec + (double)$sec;
    }
	public function redirect($url,$terminate=true,$code='302') {
        $purls = $this->getUrl($url);
		header('Location: '.$purls,true,$code);
		if($terminate) exit;
	}
	private function showMemory() {
	    return 'Memory:'.$this->getSize(memory_get_usage()).', runTime:'       .($this->microtimeFloat(microtime())-$this->microtimeFloat(_TIME)).'(s)';
	}
	protected function setPages($offset,$totals,$page,$pageLink,$pageNums=9) {
		$currPage = ceil($totals/$offset)>=$page ? $page : 0;
		$pageCtrl = new lib_page($offset, $totals, $currPage, $pageNums, $pageLink);
		return $pageCtrl->showPageHtml();
	}
	/**
     * destruct
     * show template 
     * @return null
     */
	public final function __destruct() {
		$this->_TPL ? $this->__views() : '';
        $this->debug();
		ob_end_flush();
    }
}
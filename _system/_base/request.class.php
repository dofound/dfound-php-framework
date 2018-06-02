<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class base_request 
{
	public function __construct() {
		$this->_ifesc() && $this->_normal();
	}
	/**
     * magic if open
     * 
     */
	private function _ifesc() {
		return get_magic_quotes_gpc() ? true : false;
	}
	/**
	 * get (data)
	 * @param name string
	 * @param default string
	 * @return value
	 */
	public final function get($name,$default='') {
		if ( $this->_ifesc() && isset($_GET[$name]) ) {
			return trim($_GET[$name]);
		} elseif( isset($_GET[$name]) ) {
			return addslashes(trim($_GET[$name]));
		}
        return $default;
	}
    /**
     * @param $name key
     * @return int
     * */
    public final function getInt($name) {
        $val = $this->params($name);
        return (int)$val;
    }
    /**
     * @param $name key
     * @return string
     * */
    public final function getInput($name) {
        $val = $this->params($name);
        return strip_tags($val);        
    }
    /**
     * @param $name
     * @param $default
     * @return string
     * 
     * */
    public final function getText($name,$model=0) {
        $val = $this->params($name);
        return $model ? $val : $this->filterHtml($val);         
    }
	/**
	 * post (data)
	 * @param name string
	 * @param default string
	 * @return value
	 */
	public final function post($name,$default='') {
		if ( $this->_ifesc() && isset($_POST[$name]) ) {
			return trim($_POST[$name]);
		} elseif( isset($_POST[$name]) ) {
			return addslashes(trim($_POST[$name]));
		}
        return $default;
	}
	/**
     * get all
     */
	public final function params($name,$default='') {
        $isquote = $this->_ifesc();	   
        if ( isset($_GET[$name]) ) {
            return $isquote ? trim($_GET[$name]) : addslashes(trim($_GET[$name]));
        } else if ( isset($_POST[$name]) ) {
            return $isquote ? trim($_POST[$name]) : addslashes(trim($_POST[$name]));            
        }
        return $default;
	}
	/**
	 * show t (data)
	 * @return normal value
	 */
	private function _normal() {
		if(isset($_GET))
			$_GET = $this->slashes($_GET);
		if(isset($_POST))
			$_POST=$this->slashes($_POST);
		if(isset($_REQUEST))
			$_REQUEST=$this->slashes($_REQUEST);
		if(isset($_COOKIE))
			$_COOKIE=$this->slashes($_COOKIE);
	}
	private function slashes(&$data) {
		return is_array($data) ? array_map(array($this,'slashes'),$data) : stripslashes($data);
	}
	/**
	 * get client ip address
	 * @return string xxx.xxx.xxx.xxx
	 */
	public final function getIp() {
		$unknown = 'unknown';
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']
         && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] 
        && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}	
		if (false !== strpos($ip, ',')) {
			$ip = reset(explode(',', $ip));
		}
		return $ip;
	}
    /**
     * @param $wd
     * @return string
     * */
	public function filterHtml($wd) {
		if ( is_array($wd) ) {
			foreach ($wd as $key => &$val) {
				$val = self::filterHtml($val);
			}
		}
		$farr = array("/\s /" , //trim space 
					  "/<(\/?)(script|i?frame|style|html|title|link|meta|\?|\%)([^>]*?)>/isU" ,
                      "/(<[^>]*)on[a-zA-Z] \s*=([^>]*>)/isU");
		$tarr = array(" " , " " ,"\\1\\2");
		$wd = preg_replace($farr, $tarr, $wd);
		return $wd;
	}
	/**
     * @author xiaojh@
     * get request uri
     * */
	public function getUri() {
		$requestUri=$_SERVER['REQUEST_URI'];
		if(!empty($_SERVER['HTTP_HOST'])) {
			if(strpos($requestUri,$_SERVER['HTTP_HOST'])!==false)
				$requestUri=preg_replace('/^\w+:\/\/[^\/]+/','',$requestUri);
		}else
			$requestUri=preg_replace('/^(http|https):\/\/[^\/]+/i','',$requestUri);
		return trim($requestUri);
	}
	/**
	 * protected set_url
	 * @param $type string
     * @param $url strin exmple index/test 
	 * @return array
	 */
	public final function setUrl( $type,$url='' ) {
    	$return = '';
    	switch($type) {
    		case 'www':
    			$domain = 'http://'._DOMAIN;
				if (empty($url)) return '/';
                if (_ROUTE==2) {
                    $path = $domain.'/'.$url._EXT;
				} else if (_ROUTE==1) {
					if (strrpos($url,'?')!==false) {
						$pre_purl = substr($url,0,strrpos($url,'?'));
						$purl = strrchr($url,'?');
						$url = $pre_purl.$purl;
					} else {
						$purl = explode('/',$url);
						$url = $purl[0].'/'.$purl[1];
						$purl = array_slice($purl,2);
						if (!empty($purl)) {
							foreach ($purl as $pkey=>$pval) {
								$pval = trim($pval);
								if (empty($pkey)) $url .= '?';
								$url .= $pkey%2 ?  $pval.'&' : $pval.'=';
							}
							$url = substr($url,0,-1);
						}
					}
					$path = $domain.'/'.$url;
                } else {
					$purl = explode('/',$url);
					$url = _SITE ? '?site='._SITE.'&mod='.$purl[0].'&act='.$purl[1] : '?mod='.$purl[0].'&act='.$purl[1];
					$purl = array_slice($purl,2);
					if (!empty($purl)) {
						foreach ($purl as $pkey=>$pval) {
							$pval = trim($pval);
							$url .= $pkey==0 ? '&' : '';
							$url .= $pkey%2 ?  $pval.'&' : $pval.'=';
						}
						$url = substr($url,0,-1);
					}
                    $path = $domain.'/'.$url;
                }
    			break;
    		case 'style':
    			$domain = 'http://'._DOMAIN;
    			$path = _SITE ? $domain.'/resources/'._SITE.'/'.$url : $domain.'/resources/'.$url;
    			break;
    		case 'common':
    			$domain = 'http://'._DOMAIN;
    			$path = $domain.'/resources/common/'.$url;
    			break;
    		case 'include':
    			$path = _PATH._PJ.'/'.$url;
    			break;
    	}
    	return $path;
	}    
    /**
     * @author xiaojh@
     * set uri route rule information
     * */
	public function appRoute() {
		switch ( _ROUTE ) {
			case '1':
				$this->_setUri(1);
				break;
			case '2':
				$this->_setUri(2);
				break;
			default:
				$mod = DF::app()->request->get('mod');
				$act = DF::app()->request->get('act');
				$GLOBALS['mod'] = !empty($mod) ? $mod : 'index';	//controller
				$GLOBALS['act'] = !empty($act) ? $act : 'index';	//action
				$GLOBALS['site'] = DF::app()->request->get('site');
				break;
		}
	}
    /**
     * @author xiaojh@
     * set system uri aplication rule
     * */
	private function _setUri($model) {
		$uri = $this->getUri();
		if (empty($uri)) return false;
		if ($model==2) {
            $lpost = strripos($uri,_EXT);
            if ($lpost!==false) {
                $uri = substr($uri,0,$lpost);
            }
            $lpost = strstr($uri,'?',true);
            $uri = $lpost == false ? $uri : $lpost;			
			//$uri = str_replace('?','',$uri);
			$uri = explode('/',$uri);
			array_shift($uri);
			$GLOBALS['mod'] = !empty($uri[0]) ? trim($uri[0]) : 'index';
			$GLOBALS['act'] = !empty($uri[1]) ? trim($uri[1]) : 'index';
		} else if ($model==1) {
            $lpost = strripos($uri,_EXT);
            if ($lpost!==false) {
                $uri = substr($uri,0,$lpost);
            }
			$isask = strrpos($uri,'?');
			if ($isask!==false) {
				$uri = substr($uri,0,$isask);
			}
			$uri = explode('/',$uri);
			array_shift($uri);
			$GLOBALS['mod'] = !empty($uri[0]) ? trim($uri[0]) : 'index';
			$GLOBALS['act'] = !empty($uri[1]) ? trim($uri[1]) : 'index';
		}
		if ( $model==2 && !empty($uri[1]) ) {
			$uri_array = array_slice($uri,2);
			$uri_array = array_chunk($uri_array,2);
			foreach ($uri_array as $val) {
				if (in_array($val[0],array('mod','act'))) continue;
				if (!empty($val[0])) {
					$_GET[$val[0]] = urldecode(trim($val[1]));
				}
			}
		}
	}
    /**
	 * @return string user agent
     */
	public function getUserAgent() {
		return isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
	}
	/**
	 * @return string user host name, null if cannot be determined
	 */
	public function getHost() {
		return isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:null;
	}
	/**
	 * @return string part of the request URL that is after the question mark
	 */
	public function getUrlString() {
		return isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'';
	}
	/**
	 * @return string URL referrer, null if not present
	 */
	public function getUrlReferrer() {
		return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
	}

}
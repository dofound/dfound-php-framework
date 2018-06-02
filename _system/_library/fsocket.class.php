<?php
/*=================================
*	Author:xiaojh@
*	Time:14:36 2011-10-17
*	Fun:sailor op
*	myisam first / wait....
*=================================*/
class lib_fsocket
{	
	public function __construct(){}

    /**
     * get or post
     * 
     * @author xiaojh 
     * @param $uri
     * @param $params array()
     * @param $ispost 1 / 0 : true or false
     * 
     * @return string
     * */
    public static function curlGet($uri,$params = array(),$ispost=0,$timeout = 5) {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ispost && curl_setopt($ch, CURLOPT_POST, 1);
        !empty($params) && curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params)); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);       
	    $contents = curl_exec($ch);
        curl_close($ch);
        return $contents;
    }
	/**
     * @author xiaojh@
     * @param $url string
     * @param $fileds string
     * @param $timeout
     * 
     * @return string
     * 
	 * */
	public static function curlJson($url, $fields='', $timeout = 5) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	
		curl_setopt($ch, CURLOPT_HTTPHEADER,array(
		'content-type: application/json',
		'Content-Length: '. strlen($fields)
		));
	
		$data = curl_exec($ch);
		curl_close($ch);
	
		return $data;
	}
    /**
     * curl multi
     * */
    public static function curlMulti($urls) {
        $mh = curl_multi_init();
		foreach ($urls as $k => $v) {  
			$ch[$k] = curl_init();
			curl_setopt($ch[$k], CURLOPT_URL, $v);
			curl_setopt($ch[$k], CURLOPT_RETURNTRANSFER, 1);
			curl_multi_add_handle($mh, $ch[$k]);
		}
		$ac = 0; // active
		do {  
			do{
				$mc  = curl_multi_exec($mh, $ac);  
			}while($mc==CURLM_CALL_MULTI_PERFORM);
		}while($mc==CURLM_OK && $ac && curl_multi_select($mh)!=-1);
  
		if($mc != CURLM_OK) return false;
		// retrieve data
		$ret = array();
		foreach ($url as $k => $v) {  
			if(curl_error($ch[$k]) == ''){  
				$ret[$k]=curl_multi_getcontent($ch[$k]);  
			}
			curl_multi_remove_handle($mh,$ch[$k]);  
			curl_close($ch[$k]);  
		}
		curl_multi_close($mh);
		return $ret;
    }	
	/**
	 * <p>set HTTP request</p>
	 * @param string $url	
	 * @param string $postdata	array
	 * @param string $host	
	 * @return boolean 
	 */
	public static function socket($url,$postdata,$host) {
		$da = fsockopen($host, 80, $errno, $errstr);
		if (!$da) {
			return false;
		}else {
            $postdata = http_build_query($postdata);		  
			$salida ="POST $uri  HTTP/1.1\r\n";
			$salida.="Host: $host\r\n";
			$salida.="User-Agent: PHP Script\r\n";
			$salida.="Content-Type: application/x-www-form-urlencoded\r\n";
			$salida.="Content-Length: ".strlen($postdata)."\r\n";
			$salida.="Connection: close\r\n\r\n";
			$salida.=$postdata;
			fwrite($da, $salida);
			fclose($da);
			return true;
		}
	}    
    /**
     * @param $url
     * 
     * */
	public function getFileSize($url) {  
		$url = parse_url($url); 
		if($fp = @fsockopen($url['host'],empty($url['port'])?80:$url['port'],$error))
		{ 
			fputs($fp,"GET ".(empty($url['path'])?'/':$url['path'])." HTTP/1.1\r\n"); 
			fputs($fp,"Host:$url[host]\r\n\r\n"); 
			while(!feof($fp)){ 
				$tmp = fgets($fp); 
				if(trim($tmp) == ''){ 
						break; 
				}else if(preg_match('/Content-Length:(.*)/si',$tmp,$arr)){ 
						return trim($arr[1]); 
				}
			}
		}
		return;
		
		/*
		if(trim($_POST['new_pic'])!='')
		{
			$file_size = $this->get_file_size(trim($_POST['new_pic']));
			if($file_size>52100)
			{
				$this->_alert("上传图片过大， 请重新上传");
			}
		}
		*/
	}
    /**
     * @param $file
     * @return
     * */
	public static function getXml($file) {
		$pc = @simplexml_load_file($file);
		$datas = array();
		if(!empty($pc)){
			foreach ($pc->children() as $node)
			{
				$temp = $node->children();
				$datas[] = $temp;			
			}
		}
		return $datas;
	}
	/**
	 * get or post
	 * 
	 * @author xiaojh 
	 * @param $uri
	 * @param $params array()
	 * @param $ispost 1 / 0 : true or false
	 * 
	 * @return string
	 * */
	public static function curlGetSsl($uri,$params = array(),$ispost=0,$timeout = 5,$issl=false) {
		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$ispost && curl_setopt($ch, CURLOPT_POST, 1);
		!empty($params) && curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params)); 
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $issl);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $issl);
		$contents = curl_exec($ch);
		curl_close($ch);
		return $contents;
	}
}

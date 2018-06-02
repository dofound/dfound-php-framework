<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class fun
{
    /**
     * @param $mail
     * @param $body
     * @return boolean 
     * */
    public function sentMail($mail,$body) {
        return lib_mail::sent($mail,$body);        
    }
    /**
     * down more url information
     * @param $filename
     * @param $url_array  array = array('baidu.html'=>'http://www.baidu.com',...)
     * @return void
     * 
     * */
    public static function downMultiUrl($filename,$url_array=array()) {
		$test = new lib_down();
		$test->export($filename,$url_array);
        return;        
    }     
	/**
     * down a url information
     * 
     * @param $url
     * @param $filename
     * @return void 
     * */
	public static function downSingleUrl($url,$filename) {
		lib_down::attach($url,$filename);
        return;
	}
    /**
     * convert string
     * 
     * @author xiaojh@
     * @param $str
     * @param $from_code
     * @param $to_code
     * 
     * @return string
     * */
	public static function convert($str,$from_code='gbk',$to_code="utf-8") {
		return @mb_convert_encoding($str,$to_code,$from_code);
	}
	/**
     * @author xiaojh@
     * @param $path
	 * @param $mode
	 * @param $data
	 *
     * */
	public static function writeData($path, $mode, $data)
	{ 
		$fp = fopen($path, $mode); 
		$retries = 0; 
		$max_retries = 100; 
		do { 
			if ($retries > 0) {
				usleep(rand(1, 10000));
			}
			$retries += 1; 
		}while(!flock($fp, LOCK_EX) and $retries <= $max_retries);

		if ($retries == $max_retries) {
			return false; 
		}
		fwrite($fp, "$data\n");
		flock($fp, LOCK_UN);
		fclose($fp);
		return true;
	}
    /**
     * @author xiaojh@
     * @return array
     * */
    public static function _getInt( $ids ) {
        return array_filter( explode(',', $ids),function($std){ if( preg_match('/^\d+$/',$std) ){ return true; }else{ return false; } } );        
    }
    /**
     * @author xiaojh@
     * @param $value
	 * @param $trim
	 *
     * */
	public static function isEmpty($value,$trim=false) {
		return empty($value) || $trim && is_scalar($value) && trim($value)==='';
	}
    /**
     * isCrawler
     * @return boolean true|false
     */
    public function isCrawler() {    
		if(ini_get('browscap')) {    
	         $browser= get_browser(NULL, true);    
	         if($browser['crawler']) {    
	             return true;    
	         }
	     } else if (isset($_SERVER['HTTP_USER_AGENT'])){    
	         $agent= $_SERVER['HTTP_USER_AGENT'];    
	         $crawlers= array(    
	             "/Googlebot/",    
	             "/Yahoo! Slurp;/",    
	             "/msnbot/",    
	             "/Mediapartners-Google/",    
	             "/Scooter/",    
	             "/Yahoo-MMCrawler/",    
	             "/FAST-WebCrawler/",    
	             "/Yahoo-MMCrawler/",    
	             "/Yahoo! Slurp/",    
	             "/FAST-WebCrawler/",    
	             "/FAST Enterprise Crawler/",    
	             "/grub-client-/",    
	             "/MSIECrawler/",    
	             "/NPBot/",    
	             "/NameProtect/i",    
	             "/ZyBorg/i",    
	             "/worio bot heritrix/i",    
	             "/Ask Jeeves/",    
	             "/libwww-perl/i",    
	             "/Gigabot/i",    
	             "/bot@bot.bot/i",    
	             "/SeznamBot/i",    
	         );    
	         foreach($crawlers as $c) {    
	             if(preg_match($c, $agent)) {    
	                 return true;    
	             }    
	         }    
	     }    
	     return false;    
	}
}

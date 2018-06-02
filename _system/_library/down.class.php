<?php
/**
 * 
 * 
 * */
class lib_down
{    
	public function __construct(){}
	/**
     *  $title  title
     *  $vlues  value
     *  $field  field
     *   $title = array(
     * '创建时间',
     * '服务类型',
     * '支出金额',
     * '费用类型',
     * '操作人');
     * $field = array(
     * 'optime'=>array(),
     * 'product'=>array('1'=>'下载服务'),//这里的数组表示和字段对应的渲染值比如1是女2是男 3是保密array('1'=>'女','2'=>'男','3'=>'保密')
     * 'amount'=>array('-1'=>'扣费','1'=>'在线充值','2'=>'后台充值'),
     * 'opcode'=>array(),
     * 'user_id'=>array());
     */
    public static function Excel($title, $vlues, $field) {
        $file_ending = "xls";
        $now_date = date("Y-m-d H:i:s");
		$file_name = date("YmdHis");
        echo $mydowns = iconv('UTF-8', 'GBK', "expertTime:\t " . $now_date . " \t");
        echo "\n";
        $sep = "\t";
        @header( "Content-type:   application/octet-stream "); 
        @header( "Accept-Ranges:   bytes "); 
        @header( "Content-type:application/vnd.ms-excel ");  
        @header("Content-Disposition: attachment; filename=$file_name.$file_ending");        
        @header("Content-Type: application/vnd.ms-excel; charset=GB2312");
        
        foreach ($title as $v) {
            $title = $v . "\t";
            echo $title = iconv('UTF-8', 'GBK', $title);
        }
        echo "\n";
        foreach ($vlues as $row) {
            $schema_insert = "";
           if(is_array($field)){
				foreach ($field as $fk => $fv) {
					if (!empty($fv)) {
						echo iconv('UTF-8', 'GBK', isset($fv[$row[$fk]])?$fv[$row[$fk]]:'') . "\t";
					} else {
						echo iconv('UTF-8', 'GBK', $row[$fk]) . "\t";
					}
				}
            }
            $schema_insert = str_replace($sep . "$", "", $schema_insert);
            print (trim($schema_insert));
            print "\n";
        }
    }
    /**
     * object to array
     * @param object $obj
     */
    public static function objectToArray($obj) {
    	$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    	$arr = array();
    	foreach ($_arr as $key => $val){
    		$val = (is_array($val) || is_object($val)) ? self::objectToArray($val) : $val;
    		$arr[$key] = $val;
    	}
    	return $arr;
    }
	public static function getMimeType($ext = '') {
		$mimes = array(
			'hqx'   =>  'application/mac-binhex40',
			'cpt'   =>  'application/mac-compactpro',
			'bin'   =>  'application/macbinary',
			'dms'   =>  'application/octet-stream',
			'lha'   =>  'application/octet-stream',
			'lzh'   =>  'application/octet-stream',
			'exe'   =>  'application/octet-stream',
			'class' =>  'application/octet-stream',
			'psd'   =>  'application/octet-stream',
			'so'    =>  'application/octet-stream',
			'sea'   =>  'application/octet-stream',
			'dll'   =>  'application/octet-stream',
			'oda'   =>  'application/oda',
			'pdf'   =>  'application/pdf',
			'ai'    =>  'application/postscript',
			'eps'   =>  'application/postscript',
			'ps'    =>  'application/postscript',
			'smi'   =>  'application/smil',
			'smil'  =>  'application/smil',
			'mif'   =>  'application/vnd.mif',
			'xls'   =>  'application/vnd.ms-excel',
			'ppt'   =>  'application/vnd.ms-powerpoint',
			'wbxml' =>  'application/vnd.wap.wbxml',
			'wmlc'  =>  'application/vnd.wap.wmlc',
			'dcr'   =>  'application/x-director',
			'dir'   =>  'application/x-director',
			'dxr'   =>  'application/x-director',
			'dvi'   =>  'application/x-dvi',
			'gtar'  =>  'application/x-gtar',
			'php'   =>  'application/x-httpd-php',
			'php4'  =>  'application/x-httpd-php',
			'php3'  =>  'application/x-httpd-php',
			'phtml' =>  'application/x-httpd-php',
			'phps'  =>  'application/x-httpd-php-source',
			'js'    =>  'application/x-javascript',
			'swf'   =>  'application/x-shockwave-flash',
			'sit'   =>  'application/x-stuffit',
			'tar'   =>  'application/x-tar',
			'tgz'   =>  'application/x-tar',
			'xhtml' =>  'application/xhtml+xml',
			'xht'   =>  'application/xhtml+xml',
			'zip'   =>  'application/zip',
			'mid'   =>  'audio/midi',
			'midi'  =>  'audio/midi',
			'mpga'  =>  'audio/mpeg',
			'mp2'   =>  'audio/mpeg',
			'mp3'   =>  'audio/mpeg',
			'aif'   =>  'audio/x-aiff',
			'aiff'  =>  'audio/x-aiff',
			'aifc'  =>  'audio/x-aiff',
			'ram'   =>  'audio/x-pn-realaudio',
			'rm'    =>  'audio/x-pn-realaudio',
			'rpm'   =>  'audio/x-pn-realaudio-plugin',
			'ra'    =>  'audio/x-realaudio',
			'rv'    =>  'video/vnd.rn-realvideo',
			'wav'   =>  'audio/x-wav',
			'bmp'   =>  'image/bmp',
			'gif'   =>  'image/gif',
			'jpeg'  =>  'image/jpeg',
			'jpg'   =>  'image/jpeg',
			'jpe'   =>  'image/jpeg',
			'png'   =>  'image/png',
			'tiff'  =>  'image/tiff',
			'tif'   =>  'image/tiff',
			'css'   =>  'text/css',
			'html'  =>  'text/html',
			'htm'   =>  'text/html',
			'shtml' =>  'text/html',
			'txt'   =>  'text/plain',
			'text'  =>  'text/plain',
			'log'   =>  'text/plain',
			'rtx'   =>  'text/richtext',
			'rtf'   =>  'text/rtf',
			'xml'   =>  'text/xml',
			'xsl'   =>  'text/xml',
			'mpeg'  =>  'video/mpeg',
			'mpg'   =>  'video/mpeg',
			'mpe'   =>  'video/mpeg',
			'qt'    =>  'video/quicktime',
			'mov'   =>  'video/quicktime',
			'avi'   =>  'video/x-msvideo',
			'movie' =>  'video/x-sgi-movie',
			'doc'   =>  'application/msword',
			'word'  =>  'application/msword',
			'xls'	=>  'application/excel',
			'docx'	=>	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xlsx'	=>	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'pptx'	=>	'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'eml'   =>  'message/rfc822'
		);
		return (!isset($mimes[strtolower($ext)])) ? 'application/octet-stream' : $mimes[strtolower($ext)];
	}
	/**
	* down files
	* 
	* @static
	* @param array $attach_url = array($url)
	* @param string $fname
	* @return boolean
	* 
	*/
	public static function attach($attach_url,$fname) {
		$url = parse_url($attach_url);
		if (isset($url['path'])) {
			$path = pathinfo($url['path']);
			if (isset($path['basename'])) {
				$fname = empty($fname) ? $path['basename'] : $fname;
				$data = self::curlGet($attach_url);
				$fname = mb_convert_encoding($fname, 'gbk', 'utf-8');
				header('Pragma: public');
				header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
				header('Cache-Control: no-store, no-cache, must-revalidate');
				header('Cache-Control: pre-check=0, post-check=0, max-age=0');
				header('Content-Type: application/force-download');
				header('Content-Type: '.self::getMimeType($path['extension']));
				Header('Accept-Ranges: bytes');
				header('Content-Length: '.strlen($data));
				header('Content-Disposition: attachment; filename='.$fname);
				echo $data;
				ob_end_flush();
				exit; 
			}
		}
		return false;
	}
    /**
     * out JSON
     * @param array $data
     */
	public static function outputJSON($data, $mimeType = 'application/json') {
		header('Content-type: '. $mimeType);
		echo json_encode($data);
		exit;
	}
	/**
	 * 
	 * @param $urls array('title'=>'url',...)
	 * @author
	 * 
	 * */
    public function export($title,$urls) {
        ob_end_clean();
		$fname = $title.'('.date('YmdHis') .').zip';
        $result = $this->setResult($urls);
		header('Pragma: public');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		header('Content-Type: application/force-download');
		header('Content-Transfer-Encoding: binary');
		header('Content-Encoding: none');
		header('Content-Type: application/zip');
		Header('Accept-Ranges: bytes');
		header('Content-Length: '.strlen($result));

		$agent = DF::app()->request->getUserAgent();
	    if (preg_match('/MSIE/', $agent)) {
			header('Content-Disposition: attachment; filename="'.rawurlencode($fname).'"');
		} else if (preg_match('/Firefox/', $agent)) {
			header('Content-Disposition: attachment; filename*="utf8\'\''.$fname.'"');
		} else {
			header('Content-Disposition: attachment; filename="'.$fname.'"');
		}
		echo $result;
		ob_end_flush();
		exit;
	}
	/**
	 * result
	 * @param $urls array('title'=>'url',...)
	 * @return string the zipped file 
     * 
	 */
	private function setResult($urls) {
		$dat = self::curlMulti($urls);
		if(!$dat) return;
		$n = 1;
		$zip = new lib_zipfile();
		foreach($dat as $k => $v){
			$name = mb_convert_encoding($k, 'gbk', 'utf-8');
			$zip->addFile($v, ($n++).'.'.$name);
		}
		return $zip->file();
	}
    /**
     * curl request
     * */
    private static function curlMulti($url) {
        $mh = curl_multi_init();        
		foreach ($url as $k => $v) {  
			$ch[$k] = curl_init();
			curl_setopt($ch[$k], CURLOPT_URL, $v);
			curl_setopt($ch[$k], CURLOPT_RETURNTRANSFER, 1);
			curl_multi_add_handle($mh, $ch[$k]);
		}		
		$ac = 0;
  		do {  
			do{
				$mc  = curl_multi_exec($mh, $ac);  
			}while($mc==CURLM_CALL_MULTI_PERFORM);
		}while($mc==CURLM_OK && $ac && curl_multi_select($mh)!=-1);
  
		if($mc != CURLM_OK) return false;
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
	public static function curlGet($uri,$params = array(),$ispost=0,$timeout = 10) {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ispost && curl_setopt($ch, CURLOPT_POST, 1);
        !empty($params) && curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params)); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);       
	    $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}

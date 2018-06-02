<?php
/*====================================================*
*	DoFound PHP Framework.	[end]
*	Copyright belong to Author:xiaojh
*	Have any question to contact me by dofound@163.com
*=====================================================*/
class lib_vcode
{
	public $width = 80;
	public $height = 30;
	public $backColor = 0xfffff1;
	public $padding = 1;
	public $minLength = 4;
	public $maxLength = 6;
	public $transparent = false;
	public $fontColor = 0x003366;
	public $offset = -1;
	public $fontFile;
	public $testLimit = 3;
	
	/**
	 * construct 
	 */
	public function __construct( $vals = array() ) {
		if ( !empty($vals) && is_array($vals) ) {
			foreach ($vals as $key=>$val) {
				if (in_array($key,array('width','height','backColor', 'padding','minLength','maxLength','transparent','fontColor',
				'fontColor','offset','testLimit'))) {
					$this->$key=$val;
				}
			}
		}
	}
	/**
	 * Runs the vcode.
	 */
	public function show() {
		$this->renderImage($this->getVerifyCode());
		exit();
	}
	/**
	 * Generates a new verification code.
	 * @return string the generated verification code
	 */
	protected function getVerifyCode() {
		if($this->minLength < 3)
			$this->minLength = 3;
		if($this->maxLength > 20)
			$this->maxLength = 20;
		if($this->minLength > $this->maxLength)
			$this->maxLength = $this->minLength;
		$length = mt_rand($this->minLength,$this->maxLength);

		//$letters = 'bcdfghjklmnpqrstvwxyz';
		$letters = 'ABCDEFGHJKLMNPQRSTVWXYZ3456789';
		$vowels = 'aeigrpkm';
		$code = '';
		for ($i = 0; $i < $length; ++$i) {
			if($i % 2 && mt_rand(0,15) > 2 || !($i % 2) && mt_rand(0,15) > 14)
				$code.=$vowels[mt_rand(0,7)];
			else
				$code.=$letters[mt_rand(0,29)];
		}
		//mb_convert_encoding($code,'UTF-8');
		return $code;
	}
	/**
	 * Renders the image based on the code.	example:<img src="url?" />
	 * @param string $code the verification code
	 * @return string image content	 
	 */
	protected function renderImage($code) {
		$_SESSION['vcode'] = $code;
		$image = imagecreatetruecolor($this->width,$this->height);

		$backColor = imagecolorallocate($image,
				(int)($this->backColor % 0x1000000 / 0x10000),
				(int)($this->backColor % 0x10000 / 0x100),
				$this->backColor % 0x100);
		imagefilledrectangle($image,0,0,$this->width,$this->height,$backColor);
		imagecolordeallocate($image,$backColor);

		if ($this->transparent) {
			imagecolortransparent($image,$backColor);
		}

		$fontColor = imagecolorallocate($image,
				(int)($this->fontColor % 0x1000000 / 0x10000),
				(int)($this->fontColor % 0x10000 / 0x100),
				$this->fontColor % 0x100);

		if ($this->fontFile === null) {
			$this->fontFile = dirname(__FILE__) . '/Duality.ttf';
		}
		$length = strlen($code);
		$box = imagettfbbox(30,0,$this->fontFile,$code);
		$w = $box[4] - $box[0] + $this->offset * ($length - 1);
		$h = $box[1] - $box[5];
		$scale = min(($this->width - $this->padding * 2) / $w,($this->height - $this->padding * 2) / $h);
		$x = 10;
		$y = round($this->height * 30 / 40);
		for ($i = 0; $i < $length; ++$i) {
			$fontSize = (int)(rand(26,32) * $scale * 0.8);
			$angle = rand(-10,10);
			$letter = $code[$i];
			$box = imagettftext($image,$fontSize,$angle,$x,$y,$fontColor,$this->fontFile,$letter);
			$x = $box[2] + $this->offset;
		}
		imagecolordeallocate($image,$fontColor);
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Transfer-Encoding: binary');
		header("Content-type: image/png");
		imagepng($image);
		imagedestroy($image);
	}

}

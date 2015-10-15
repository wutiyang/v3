<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Captcha {

	protected $_width = false;
	protected $_height = false;
	protected $_word = false;

	protected $_color_bg = false;
	protected $_color_pix = false;
	protected $_color_word = false;


	public function __construct(){}

	public function reset(){
		$this->_width = false;
		$this->_height = false;
		$this->_word = false;
		$this->_color_bg = false;
		$this->_color_pix = false;
		$this->_color_word = false;
	}

	public function setWidth($width){
		$this->_width = $width;
	}

	public function setHeight($height){
		$this->_height = $height;
	}

	public function setWord($word){
		$this->_word = $word;
	}

	public function setColorBg($color_bg){
		$this->_color_bg = $color_bg;
	}

	public function setColorPix($color_pix){
		$this->_color_pix = $color_pix;
	}

	public function setColorWord($color_word){
		$this->_color_word = $color_word;
	}

	public function generate(){
		@header("Content-Type:image/png");
		$im = imagecreate($this->_width,$this->_height);
		$back = imagecolorallocate($im,$this->_color_bg[0],$this->_color_bg[1],$this->_color_bg[2]);
		$pix = imagecolorallocate($im,$this->_color_pix[0],$this->_color_pix[1],$this->_color_pix[2]);
		$font = imagecolorallocate($im,$this->_color_word[0],$this->_color_word[1],$this->_color_word[2]);
		for($i=0;$i<1000;$i++) imagesetpixel($im,mt_rand(0,$this->_width),mt_rand(0,$this->_height),$pix);
		imagestring($im,5,7,3,$this->_word,$font);
		imagerectangle($im,0,0,$this->_width-1,$this->_height-1,$font);
		imagepng($im);
		imagedestroy($im);
	}
}

/* End of file captcha.php */
/* Location: ./application/libraries/captcha.php */
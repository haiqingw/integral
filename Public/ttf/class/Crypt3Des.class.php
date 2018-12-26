<?php
class Crypt3Des{
	protected $key = ""; //密钥 要与java的转化成的16进制字符串对应
	function __construct($key){
		$this->key = $key;
	}
	//数据加密
	function encrypt($input){
		$size = mcrypt_get_block_size(MCRYPT_3DES,'ecb');
		$input = $this->pkcs5_pad($input,$size);
		$key = str_pad($this->key,24,'0');
		$td = mcrypt_module_open(MCRYPT_3DES,'','ecb','');
		$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
		@mcrypt_generic_init($td,$key,$iv);
		$data = mcrypt_generic($td,$input);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$data = base64_encode($data);
		return $data;
	}
	//数据解密
	function decrypt($encrypted){
		$encrypted = base64_decode($encrypted);
		$key = str_pad($this->key,24,'0');
		$td = mcrypt_module_open(MCRYPT_3DES,'','ecb','');
		$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
		$ks = mcrypt_enc_get_key_size($td);
		@mcrypt_generic_init($td,$key,$iv);
		$decrypted = mdecrypt_generic($td,$encrypted);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$y = $this->pkcs5_unpad($decrypted);
		return $y;
	}
	function pkcs5_pad($text, $blocksize){
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad),$pad);
	}
	function pkcs5_unpad($text){
		$pad = ord($text{strlen($text) - 1});
		if($pad > strlen($text)){
			return false;
		}
		if(strspn($text,chr($pad),strlen($text) - $pad) != $pad){
			return false;
		}
		return substr($text,0,-1 * $pad);
	}
}
/*
$des = new Crypt3Des();
$des->key = "CHINAPNR";
$rs1 = "AaOHNU/S2vIj2/5V+RIvyJex6i7FkxSo36Sqnln4e8DzznygOufcDEGnQ/UfrXdLgaobceaB8wO6Ym3JIoQ1CIaWx+TbYh2Tw3p8V2Qvpn0lIod6aLyQEMULAl6WgVC+s0XGWGFSdAv+Zoxt6gOAd4WvayNbqC3MengP8NDMwCfKART6zhdTJkClaRfZZ7udjiZFEwM7zBOfw8YB9aMiLTRUPW9EFVVkaCCHq2kbXfAZMs++GC4qRtRMZuuWOv1ymi2FRrLxoe8plQqRVCETG3g+8TYfZvPZTgNy9yVdA8vicyVtOkzwwf1n2ltHEmGJKN806Zb8EswIVq3AU8120OnOMEAVi9EoDe3lXItrytpVfy3L+y+g87kfdmySmjLK2HGOOT9WMf56fDXT7P2ZeVrqqqElEZIqEQKHrl5WBckPGsouuXG43k89MO4XU0qORaACi7APRbfxjBD0LseEK5AW54ROw234T542dHWfjou6ejPE/uuv/EGHJd/VAFkWuITzeqoUvrrZT1B+dw01yITNs5oA2k5/qdz+kqaNO1tV28zy8Awr2jp4+O8PCnXuMTTlQCjKM8eYHoa1ihyZ+COoaTqGpX4WtvKg6LfANk4QaADB/SMIk5ex6i7FkxSoa4kOtGmDzpzFU1GtfJzWJ6mYzWKXujbzhkABOT+Hu+UNW0JYADOA+BOXUNXrd31NZpbnB+19IlV9u7qL/6XTjP2gmP7D3zFYdzWtkrsNo1xHTDdu3HIw5FprfYU/z7Qvr2jmmysKAgYf8/tQ671oJxk8lX0I+UkTWabzi3MHzyWEqt+D8jKUXp1xM6W80pDC1MXt3APabbwWBUckIvE2EzSySN8Av2mf05UGRvAXShISoILC+/LGBt4DGwDx4sIRXRBWJZrNSPu8T0BUW6G5OdgoSK+4x5slh3i6znQDx/8bjJFHNKaYiw==";
$rs2 = $des->decrypt($rs1);
print_r($rs2);
 */
?>  

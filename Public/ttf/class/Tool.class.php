<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2016年10月21日 下午4:16:15
# Filename: Tool.class.php
# Description: 工具箱
#================================================
header("Content-Type:text/html;charset=utf-8");
define("PUB_TTF","./Public/ttf/class/");
define("P_URL","http://pay.xylrcs.cn");
define("T_URL","http://paytest.xylrcs.cn");
define("HFT_PUBLIC_KEY","2017_hft_rsa_pub.pem"); // 华付通公钥
define("MY_PUBLIC_KEY","2017_my_rsa_pub.pem");	 // 自己的公钥
define("MY_PRIVATE_KEY","2017_my_rsa_pri.pem");	 // 自己的私钥
include_once ("CryptAES.class.php");
//异常类
class MyException extends Exception{
	public function errorMessage(){
		return $this->getMessage();
	}
}
//工具类
class Tool{
	/**
	 * 获取Uploads路径
	 * @param string|array $vars
	 */
	static function GP($vars){
		if(empty($vars)){
			return false;
		}
		$filePath = array(
			"./Uploads/"
		);
		if(gettype($vars) == "string"){
			$filePath[] = $vars . "/";
		}
		if(self::isArray($vars)){
			for($i = 0;$i < count($vars);$i++){
				$filePath[] = $vars[$i] . "/";
			}
		}
		for($i = 0;$i < count($filePath);$i++){
			$newFilePath .= $filePath[$i];
			if(!is_dir($filePath[$i]))
				mkdir($newFilePath);
		}
		return $newFilePath;
	}
	/**
	 * 添加logs
	 * @param array $arr
	 * @param string $type
	 */
	static function aLogs($arr, $type){
		$baseUrl = self::GP([
			"EasyPay", 
			$type
		]);
		if(!is_dir($baseUrl)){
			@mkdir($baseUrl);
		}
		$url = $baseUrl . $type . date("Y-m-d") . ".txt";
		file_put_contents($url,var_export($arr,true) . "\n",FILE_APPEND);
	}
	/**
	 * urlencode
	 * @param string $str
	 */
	static function UE($key, $val, $type){
		$ueArr = [
			"pay_notifyurl", 
			"pay_accountname", 
			"pay_bankname", 
			"pay_fenbankname", 
			"pay_zhibankname", 
			"pay_province", 
			"pay_city"
		];
		if($type == "DF" && in_array($key,$ueArr)){
			return urlencode($val);
		}
		return $val;
	}
	/**
	 * 获取订单号
	 * @param string $type
	 */
	static function GO($type){
		return "YDSM" . $type . date("YmdHis") . self::getRandom(2);
	}
	/**
	 * get sign
	 * @param array $requestarray
	 */
	static function GS($requestarray, $type = "DF"){
		//dump($requestarray);
		if($type == "SP"){
			ksort($requestarray);
			reset($requestarray);
		}
		$md5str = "";
		foreach($requestarray as $key => $val){
			$md5str = $md5str . $key . "=" . self::UE($key,$val,$type) . "&";
		}
		$md5str = rtrim($md5str,"&") .KEY;
// 		echo htmlspecialchars($md5str);
		$sign = strtoupper(md5($md5str));
		return $sign;
	}
	/**
	 * array转get
	 * @param array $arr
	 * @throws MyException
	 * @return string
	 */
	static function arrayToget($arr){
		if(!is_array($arr)){
			throw new MyException("not is array");
		}
		$str = "?";
		foreach($arr as $key => $val){
			$str .= $key . "=" . $val . "&";
		}
		return rtrim($str,"&");
	}
	/*
	 * curl get post
	 */
	static function curl_request($url, $data = null, $second = 120){
		$curl = curl_init();
		curl_setopt($curl,CURLOPT_TIMEOUT,$second);
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
		if(!empty($data)){
			curl_setopt($curl,CURLOPT_POST,1);
			curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
		}
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
	/**
	 * curl json
	 */
	static function curl_json($url, $data){
		$data_string = json_encode($data);
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
			'Content-Type: application/json', 
			'Content-Length: ' . strlen($data_string)
		));
		$output = curl_exec($ch);
		return $output;
	}
	/* 	static function sign($content){
	 $rsaPrivateKeyPem = PUB_TTF . 'hpay_keys/rsa_pri.pem';
	 $priKey = file_get_contents($rsaPrivateKeyPem);
	 $res = openssl_get_privatekey($priKey);
	 openssl_sign($content,$sign,$res);
	 openssl_free_key($res);
	 $sign = base64_encode($sign);
	 return $sign;
	 } */
	/**
	 * SHA1WithRSA 获取sign 私钥加密
	 * @param string $data
	 */
	static function sign($data){
		$rsaPrivateKeyPem = PUB_TTF . 'hpay_keys/' . MY_PRIVATE_KEY;
		$key = openssl_pkey_get_private(file_get_contents($rsaPrivateKeyPem));
		openssl_sign($data,$sign,$key,OPENSSL_ALGO_SHA1);
		$sign = base64_encode($sign);
		return $sign;
	}
	/**
	 * 验证sign 公钥解密
	 * @param string $data
	 * @param string $sign
	 */
	static function wjVerify($data, $sign){
		$rsaPublicKeyPem = PUB_TTF . 'hpay_keys/' . MY_PUBLIC_KEY;
		$sign = base64_decode($sign);
		$key = openssl_pkey_get_public(file_get_contents($rsaPublicKeyPem));
		$result = openssl_verify($data,$sign,$key,OPENSSL_ALGO_SHA1) === 1;
		return $result;
	}
	/**
	 * 公钥加密
	 * @param string $sourcestr
	 * @param string $fileName
	 * @return string
	 */
	static function publickey_encodeing($sourcestr, $fileName){
		$key_content = file_get_contents($fileName);
		$pubkeyid = openssl_get_publickey($key_content);
		if(openssl_public_encrypt($sourcestr,$crypttext,$pubkeyid)){
			return base64_encode("" . $crypttext);
		}
	}
	/**
	 * 私钥解密
	 * @param string $crypttext
	 * @param string $fileName
	 * @param string $fromjs
	 */
	static function privatekey_decodeing($crypttext, $fileName, $fromjs = FALSE){
		$key_content = file_get_contents($fileName);
		$prikeyid = openssl_get_privatekey($key_content);
		$crypttext = base64_decode($crypttext);
		$padding = $fromjs ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;
		if(openssl_private_decrypt($crypttext,$sourcestr,$prikeyid,$padding)){
			return $fromjs ? rtrim(strrev($sourcestr),"\0") : "" . $sourcestr;
		}
		return;
	}
	/**
	 * 公钥解密
	 * @param string $crypttext
	 * @param string $fileName
	 * @param string $fromjs
	 */
	static function publickey_decodeing($crypttext, $fileName = "keys/rsa_pub_yl.pem", $fromjs = FALSE){
		$key_content = file_get_contents(PUB_TTF.$fileName);
		//$pi_key =  openssl_pkey_get_private($key_content);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
		//$pu_key = openssl_pkey_get_public($key_content);//这个函数可用来判断公钥是否是可用的
		$prikeyid = openssl_pkey_get_public($key_content);
		$crypttext = base64_decode($crypttext);
		dump($prikeyid);
		$padding = $fromjs ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;
// 		if(openssl_public_decrypt($crypttext,$sourcestr,$prikeyid,$padding)){
		dump(openssl_public_decrypt($crypttext,$sourcestr,$prikeyid,$padding));
		dump($sourcestr);
// 			return $fromjs ? rtrim(strrev($sourcestr),"\0") : "" . $sourcestr;
// 		}
		return;
	}
	/**
	 * RSA加密解密
	 * @param string $string
	 * @param string $type EN 默认php加密 DE php解密 JS js解密
	 * @return string|boolean
	 */
	static function RSAcode($string, $type = "EN"){
		if(!empty($string)){
			$publicKey = PUB_TTF . 'keys/rsa_pub.pem';
			$privateKey = PUB_TTF . 'keys/rsa_pri.pem';
			if($type == "EN"){
				return self::publickey_encodeing($string,$publicKey);
			}elseif($type == "DE"){
				return self::privatekey_decodeing($string,$privateKey);
			}elseif($type == "JS"){
				$receiveKey = base64_encode(pack("H*",$string));
				return self::privatekey_decodeing($receiveKey,$privateKey,TRUE);
			}
		}
		return false;
	}
	/**
	 * AES加密解密
	 * @param string $string
	 * @param string $type
	 */
	static function AEScode($string, $key = "", $type = "EN"){
		if($type == "DE"){
			$keyStr = $key;
		}else{
			$keyStr = self::getRandom(16);
		}
		$aes = new CryptAES();
		$aes->set_key($keyStr);
		$aes->require_pkcs5();
		if($type == "DE"){
			return $aes->decrypt($string);
		}
		$encText = $aes->encrypt($string);
		return [
			$keyStr, 
			$encText
		];
	}
	/**
	 * 判断是否数组且不为空
	 * @param array $arr
	 */
	static function isArray($arr){
		if(is_array($arr) && count($arr) > 0){
			return true;
		}
		return false;
	}
	/**
	 * 获取随机字母
	 * @param number $length
	 * @return string
	 */
	static function getRandom($length = 2){
		$string = "";
		$str = "abcdefghijklmnopqrstuvwsyz";
		for($i = 0;$i < $length;$i++){
			$string .= $str[rand(0,strlen($str) - 1)];
		}
		return $string;
	}
	/**
	 * 数组转换成json字符串
	 * @param array $arr
	 */
	static function toJsonString($arr){
		if(!self::isArray($arr)){
			return false;
		}
		$str = "{";
		foreach($arr as $key => $val){
			$str .= "\"" . $key . "\":" . $val . ",";
		}
		$str = rtrim($str,",");
		$str .= "}";
		return $str;
	}
	/**
	 * 对变量进行 JSON 编码
	 * @param mixed value 待编码的 value ，除了resource 类型之外，可以为任何数据类型，该函数只能接受 UTF-8 编码的数据
	 * @return string 返回 value 值的 JSON 形式
	 */
	static function json_encode_ex($value){
		if(version_compare(PHP_VERSION,'5.4.0','<')){
			$str = json_encode($value);
			$str = preg_replace_callback("#\\\u([0-9a-f]{4})#i",function ($matchs){
				return iconv('UCS-2BE','UTF-8',pack('H4',$matchs[1]));
			},$str);
			return $str;
		}else{
			return json_encode($value,JSON_UNESCAPED_UNICODE);
		}
	}
	/*RSA 分段加密开始*/
	static function rsa_publickey_encrypt($data){
		$pubk = file_get_contents(PUB_TTF . 'hpay_keys/' . HFT_PUBLIC_KEY);
		$pubk = openssl_get_publickey($pubk);
		openssl_public_encrypt($data,$en,$pubk,OPENSSL_PKCS1_PADDING);
		return $en;
	}
	static function rsa_privatekey_decrypt($data){
		$prik = file_get_contents(PUB_TTF . 'hpay_keys/' . MY_PRIVATE_KEY);
		$prik = openssl_get_privatekey($prik);
		openssl_private_decrypt($data,$de,$prik,OPENSSL_PKCS1_PADDING);
		return $de;
	}
	static function rsa_publickey_decrypt($data){
		$prik = file_get_contents(PUB_TTF . 'keys/rsa_pub_yl.pem');
		$prik = openssl_get_publickey($prik);
		openssl_public_decrypt($data,$de,$prik,OPENSSL_PKCS1_PADDING);
		return $de;
	}
	static function rsa_publickey_encrypt_yl($data){
		$pubk = file_get_contents(PUB_TTF . 'keys/rsa_pub_yl.pem');
		$pubk = openssl_get_publickey($pubk);
		openssl_public_encrypt($data,$en,$pubk,OPENSSL_PKCS1_PADDING);
		return $en;
	}
	static function rsa_encrypt($data, $rsa_bit = 2048){
		$inputLen = strlen($data);
		$offSet = 0;
		$i = 0;
		$maxDecryptBlock = $rsa_bit / 8 - 11;
		$en = '';
		// 对数据分段加密
		while($inputLen - $offSet > 0){
			if($inputLen - $offSet > $maxDecryptBlock){
				$cache = self::rsa_publickey_encrypt(substr($data,$offSet,$maxDecryptBlock));
			}else{
				$cache = self::rsa_publickey_encrypt(substr($data,$offSet,$inputLen - $offSet));
			}
			$en = $en . $cache;
			$i++;
			$offSet = $i * $maxDecryptBlock;
		}
		return $en;
	}
	static function rsa_decrypt($data, $rsa_bit = 2048){
		$inputLen = strlen($data);
		$offSet = 0;
		$i = 0;
		$maxDecryptBlock = $rsa_bit / 8;
		$de = '';
		$cache = '';
		// 对数据分段解密
		while($inputLen - $offSet > 0){
			if($inputLen - $offSet > $maxDecryptBlock){
				$cache = self::rsa_privatekey_decrypt(substr($data,$offSet,$maxDecryptBlock));
			}else{
				$cache = self::rsa_privatekey_decrypt(substr($data,$offSet,$inputLen - $offSet));
			}
			$de = $de . $cache;
			$i = $i + 1;
			$offSet = $i * $maxDecryptBlock;
		}
		return $de;
	}
	static function rsa_pubdecrypt($data, $rsa_bit = 1024){
		$inputLen = strlen($data);
		$offSet = 0;
		$i = 0;
		$maxDecryptBlock = $rsa_bit / 8;
		$de = '';
		$cache = '';
		// 对数据分段解密
		while($inputLen - $offSet > 0){
			if($inputLen - $offSet > $maxDecryptBlock){
				$cache = self::rsa_publickey_decrypt(substr($data,$offSet,$maxDecryptBlock));
			}else{
				$cache = self::rsa_publickey_decrypt(substr($data,$offSet,$inputLen - $offSet));
			}
			$de = $de . $cache;
			$i = $i + 1;
			$offSet = $i * $maxDecryptBlock;
		}
		return $de;
	}
	static function rsa_pubencrypt($data, $rsa_bit = 1024){
		$data = json_encode($data);
		$inputLen = strlen($data);
		$offSet = 0;
		$i = 0;
		$maxDecryptBlock = $rsa_bit / 8 - 11;
		$en = '';
		// 对数据分段加密
		while($inputLen - $offSet > 0){
			if($inputLen - $offSet > $maxDecryptBlock){
				$cache = self::rsa_publickey_encrypt_yl(substr($data,$offSet,$maxDecryptBlock));
			}else{
				$cache = self::rsa_publickey_encrypt_yl(substr($data,$offSet,$inputLen - $offSet));
			}
			$en = $en . $cache;
			$i++;
			$offSet = $i * $maxDecryptBlock;
		}
		return base64_encode($en);
	}
	static function pubencrypt($data){
		$crypted = [];
		$data = json_encode($data);
	
		$publicKey = openssl_pkey_get_public(file_get_contents(PUB_TTF . 'keys/rsa_pub_yl.pem'));
		$dataArray = str_split($data, 117);
		foreach($dataArray as $subData){
			$subCrypted = null;
			openssl_public_encrypt($subData, $subCrypted, $publicKey,OPENSSL_PKCS1_PADDING);
			$crypted[] = $subCrypted;
		}
		return base64_encode(implode('',$crypted));
	}
	/*RSA 分段加密结束*/
}
?>
<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2016年9月19日 下午3:41:41
# Filename: CBank.class.php
# Description: 央联支付接口
#================================================
header("Content-Type:text/html;charset=utf-8");
define("BUS_ID","10053");
define("KEY","PhNKK2am8CclaSIpOS6Wjv3vXwml3a");
define("SER_URL","http://pay.bank-pay.com.cn/Pay_Index.html");
define("P_URL","http://pay.xylrcs.cn");
define("T_URL","http://paytest.xylrcs.cn");
define("C_F","/index.php/EasyApp/SecondsTo/");
/**
 * tongdao 
 * 民生微信: MinShengWx
 * 民生支付宝: MinShengZfb
 * 兴业微信扫码: XingYeWeiXinSm
 * 银联: Unionpay
 * 支付宝：ShangHaiZfb  微信：ShangHaiWx
 * 
 * pay_bankcode
 * 微信: wxzf
 * 支付宝: zfbzf
 * 银联: ICBC
 * 
 * @author hojk
 */
class CBank{
	protected $ScanPayParams = [
		"pay_memberid" => BUS_ID, 
		"pay_orderid" => "", 
		"pay_amount" => "", 
		"pay_applydate" => "", 
		"pay_bankcode" => "wxzf", 
		"pay_notifyurl" => "", 
		"pay_callbackurl" => ""
	];
	protected $DaiFuParams = [
		"pay_memberid" => BUS_ID, 
		"pay_jiesuan" => "jiesuan", 
		"pay_amount" => 0, 
		"pay_notifyurl" => "", 
		"pay_bankaccount" => "", 
		"pay_accountname" => "", 
		"pay_bankname" => "", 
		"pay_fenbankname" => "", 
		"pay_zhibankname" => "", 
		"pay_province" => "", 
		"pay_city" => ""
	];
	protected $Reserved = "";
	protected $Reserved1 = "";
	protected $Reserved2 = "";
	protected $Reserved3 = "";
	/**
	 * 银联支付
	 */
	public function UnionPay(){
		$this->setParams("pay_bankcode","ICBC");
		$this->setParams("pay_notifyurl",P_URL . C_F . "notifyUnionPayWallet");
		$this->setParams("pay_callbackurl",P_URL . C_F . "callbackUnionPayWallet");
		$this->setParams("pay_applydate",dateFormat(time(),2));
		// 		$this->setParams("pay_orderid","YDSM_SM_" . time() . rand(1,100) );
		$params = $this->ScanPayParams;
		if(!is_numeric($params['pay_amount']) || $params['pay_amount'] < 1){
			throw new CBkException("金额不正确,请填写1元以上的数值");
		}
		$sign = Tool::GS($params,"SP");
		$this->setParams("pay_reserved2",$this->Reserved2);
		//$this->setParams("wxreturntype","QR");
		$this->setParams("pay_md5sign",$sign);
		$this->setParams("tongdao","Unionpay");
		//$response = Tool::curl_request(SER_URL,$this->ScanPayParams);
		return $this->getForm();
	}
	protected function getForm(){
		$form = "<script>window.onload=function(){document.getElementById('myForm').click();}</script><form action='".SER_URL."' method='post'>";
		foreach($this->ScanPayParams as $key=>$val){
			$form .= "<input type='hidden' name='".$key."' value='".$val."'/>";
		}
		$form .= "<input type='submit' style='display:none' id='myForm'></form>";
		return $form;
	}
	public function Recharge(){
		$this->setParams("pay_bankcode","wxzf");
// 		$this->setParams("pay_notifyurl","http://dz.xylrcs.cn/index.php/Admin/AdRecharge/notifyRecharge");
// 		$this->setParams("pay_callbackurl","http://dz.xylrcs.cn/index.php/Admin/AdRecharge/callbackRecharge");
		$this->setParams("pay_applydate",dateFormat(time(),2));
		// 		$this->setParams("pay_orderid","YDSM_SM_" . time() . rand(1,100) );
		$params = $this->ScanPayParams;
		if(!is_numeric($params['pay_amount']) || $params['pay_amount'] < 1){
			throw new CBkException("金额不正确,请填写1元以上的数值");
		}
		$sign = Tool::GS($params,"SP");
		$this->setParams("pay_reserved2",$this->Reserved2);
		$this->setParams("wxreturntype","QR");
		$this->setParams("pay_md5sign",$sign);
		$this->setParams("tongdao","ShangHaiWx");
		$response = Tool::curl_request(SER_URL,$this->ScanPayParams);
		if(gettype(stripos($response,"weixin://")) == "boolean"){
			$flag = 0;
		}else{
			$flag = 1;
		}
		return [
				"status" => $flag,
				"msg" => str_replace(array("\r\n", "\r", "\n","﻿"), "", trim($response))
		];
	}
	public function Supermarket(){
		$this->setParams("pay_bankcode","wxzf");
		$this->setParams("pay_notifyurl","http://ydc.xylrcs.cn/index.php/EasyApp/Wxpay/notifySupermarket");
		$this->setParams("pay_callbackurl","http://ydc.xylrcs.cn/index.php/EasyApp/Wxpay/callbackSupermarket");
		$this->setParams("pay_applydate",dateFormat(time(),2));
		// 		$this->setParams("pay_orderid","YDSM_SM_" . time() . rand(1,100) );
		$params = $this->ScanPayParams;
		if(!is_numeric($params['pay_amount']) || $params['pay_amount'] < 1){
			throw new CBkException("金额不正确,请填写1元以上的数值");
		}
		$sign = Tool::GS($params,"SP");
		$this->setParams("pay_reserved2",$this->Reserved2);
		$this->setParams("wxreturntype","QR");
		$this->setParams("pay_md5sign",$sign);
		$this->setParams("tongdao","ShangHaiWx");
		$response = Tool::curl_request(SER_URL,$this->ScanPayParams);
		if(gettype(stripos($response,"weixin://")) == "boolean"){
			$flag = 0;
		}else{
			$flag = 1;
		}
		return [
				"status" => $flag,
				"msg" => str_replace(array("\r\n", "\r", "\n","﻿"), "", trim($response))
		];
	}
	/**
	 * 微信扫码支付
	 */
	public function ScanPay(){
		$this->setParams("pay_bankcode","wxzf");
		$this->setParams("pay_notifyurl",P_URL . C_F . "notifyScanPayWallet");
		$this->setParams("pay_callbackurl",P_URL . C_F . "callbackScanPayWallet");
		$this->setParams("pay_applydate",dateFormat(time(),2));
		// 		$this->setParams("pay_orderid","YDSM_SM_" . time() . rand(1,100) );
		$params = $this->ScanPayParams;
		if(!is_numeric($params['pay_amount']) || $params['pay_amount'] < 1){
			throw new CBkException("金额不正确,请填写1元以上的数值");
		}
		$sign = Tool::GS($params,"SP");
		$this->setParams("pay_reserved2",$this->Reserved2);
		$this->setParams("wxreturntype","QR");
		$this->setParams("pay_md5sign",$sign);
		$this->setParams("tongdao","ShangHaiWx");
		$response = Tool::curl_request(SER_URL,$this->ScanPayParams);
		if(gettype(stripos($response,"weixin://")) == "boolean"){
			$flag = 0;
		}else{
			$flag = 1;
		}
		return [
			"status" => $flag, 
			"msg" => str_replace(array("\r\n", "\r", "\n","﻿"), "", trim($response))
		];
	}
	/**
	 * 支付宝扫码支付
	 */
	public function AliPay(){
		$this->setParams("pay_bankcode","zfbzf");
		$this->setParams("pay_notifyurl",P_URL . C_F . "notifyAliPayWallet");
		$this->setParams("pay_callbackurl",P_URL . C_F . "callbackAliPayWallet");
		$this->setParams("pay_applydate",dateFormat(time(),2));
		// 		$this->setParams("pay_orderid","YDSM_SM_" . time() . rand(1,100) );
		$params = $this->ScanPayParams;
		if(!is_numeric($params['pay_amount']) || $params['pay_amount'] < 1){
			throw new CBkException("金额不正确,请填写1元以上的数值");
		}
		$sign = Tool::GS($params,"SP");
		$this->setParams("pay_reserved2",$this->Reserved2);
		$this->setParams("wxreturntype","QR");
		$this->setParams("pay_md5sign",$sign);
		$this->setParams("tongdao","ShangHaiZfb");
		$response = Tool::curl_request(SER_URL,$this->ScanPayParams);
		if(gettype(stripos($response,"alipay")) == "boolean"){
			$flag = 0;
		}else{
			$flag = 1;
		}
		return [
				"status" => $flag,
				"msg" => str_replace(array("\r\n", "\r", "\n","﻿"), "", trim($response))
		];
	}
	/**
	 * pay for another
	 */
	public function DaiFu(){
		$this->setParams("pay_notifyurl",P_URL . C_F . "notifyDaiFu","DF");
		$this->setParams("pay_fenbankname","分行","DF");
		$this->setParams("pay_zhibankname","支行","DF");
		$this->setParams("pay_province","中国","DF");
		$params = $this->DaiFuParams;
		if(empty($params['pay_bankaccount']) || !strlen($params['pay_accountname']) || !strlen($params['pay_bankname'])){
			throw new CBkException("银行名称与银行卡号与持卡人姓名不能为空");
		}
		if(!is_numeric($params['pay_amount']) || $params['pay_amount'] <= 0){
			throw new CBkException("金额不正确,请填写0元以上的数值");
		}
		$sign = Tool::GS($params);
		$this->setParams("pay_reserved",$this->Reserved,"DF");
		$this->setParams("pay_md5sign",$sign,"DF");
		$response = Tool::curl_request(SER_URL,$this->DaiFuParams);
		if($response == "00"){
			$status = [
				"status" => 1, 
				"msg" => "打款成功"
			];
		}else{
			$status = [
				"status" => 0, 
				"msg" => $response
			];
		}
		return $status;
	}
	public function setReserved($val, $type = 0){
		switch($type){
			case 0:
				$this->Reserved = trim($val);
				break;
			case 1:
				$this->Reserved1 = trim($val);
				break;
			case 2:
				$this->Reserved2 = trim($val);
				break;
			case 3:
				$this->Reserved3 = trim($val);
				break;
		}
	}
	/**
	 * 设置参数
	 * @param string $key
	 * @param string $val
	 * @param string $type
	 */
	public function setParams($key, $val, $type = "SP"){
		switch($type){
			case "SP":
				$this->ScanPayParams[$key] = trim($val);
				break;
			case "DF":
				$this->DaiFuParams[$key] = trim($val);
				break;
		}
	}
	/**
	 * 获取参数
	 * @param string $key
	 * @param string $type
	 * @return string|unknown
	 */
	public function getParams($key, $type = "SP"){
		if($type == "SP"){
			$val = $this->ScanPayParams[$key];
		}elseif($type == "DF"){
			$val = $this->DaiFuParams[$key];
		}
		return $val;
	}
}
//异常类
class CBkException extends Exception{
	public function errorMessage(){
		return $this->getMessage();
	}
}
//工具类
class Tool{
	/**
	 * 添加logs
	 * @param array $arr
	 * @param string $type
	 */
	static function aLogs($arr,$type){
		$baseUrl = "./Uploads/SecondsTo/";
		if(!is_dir($baseUrl)){
			@mkdir($baseUrl);
		}
		$url = $baseUrl.$type.date("Y-m-d").".txt";
		file_put_contents($url,var_export($arr,true). "\n",FILE_APPEND);
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
	 * get sign
	 * @param array $requestarray
	 */
	static function GS($requestarray, $type = "DF"){
		if($type == "SP"){
			ksort($requestarray);
			reset($requestarray);
		}
		$md5str = "";
		foreach($requestarray as $key => $val){
			$md5str = $md5str . $key . "=>" . self::UE($key,$val,$type) . "&";
		}
		$md5str = $md5str . "key=" . KEY;
		$sign = strtoupper(md5($md5str));
		return $sign;
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
}
?>

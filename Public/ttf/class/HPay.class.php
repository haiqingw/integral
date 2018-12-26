<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2016年10月21日 下午4:16:04
# Filename: HPay.class.php
# Description: 华付通支付接口
#================================================
include_once ("Tool.class.php");
define("QD_ID","861100021");
define("BUS_ID","828430933056425984");
define("PAR_ID","0000000001");
define("SER_URL","http://api.huaepay.com/gateway/");
define("C_F","/index.php/EasyApp/SecondsTo/");
$date = date('YmdHis');
define("reqTime",$date);
define("LS_NUM","LS" . $date);
class HPay{
/* 	protected $methods = [
		"rw" => "merchant.join",  //商户入网
		"rwcx" => "merchant.join.query",  //商户入网状态查询
		"qbcx" => "merchant.wallet.query",  //商户钱包查询
		"jszh" => "merchant.card.update",  //商户结算账户变更
		"jsfl" => "merchant.rate.update",  //商户结算费率变更
		"tx" => "merchant.withdrawals",  //商户提现
		"sm" => "trade.order",  //扫码支付
		"gzh" => "trade.order.official",  //公众号支付
		"ddcx" => "trade.query",  //订单信息查询
		"dz" => "channel.file.get"
	]; //获取对账文件 */
	/**
	 * 提交参数
	 * @var array
	 */
	protected $mainParams = [
		"head" => [
			"method" => "",  //请求方法服务名
			"channelNo" => QD_ID,  //渠道编号
			"userReqNo" => LS_NUM,  //平台方请求流水号
			"reqTime" => reqTime,  //平台方请求时间
			"sign" => "",  //签名串
			"encryptKey" => "",  //加密串
			"version" => "V1.0.0"
		], 
		"body" => ""
	];
	/**
	 * 返回报文解密
	 * @param string $response
	 */
	public function decode($response, $notify = false){
		if(empty($response)){
			return false;
		}
		//$response = '{"head":{"version":"V1.0.0","userReqNo":"LS20161021111828","reqTime":"20161021111826","respTime":null,"respNo":"789304800494882816","respCode":"000000","respDesc":"交易成功","sign":"tqg9noHGku3jRbjSxxsOd/uuh7bQBVfZ/PZuVqs6yga5IaslD2Hi8kd6pGfCYKDxIR1O31jaaDs7TRC70ceP912Mua9JodlvQpYq5vIze954K8SP9TdOJ0NS6UBFytpoZx8QZvAM5IAhnNzrZfgIYHOtk0iXR8x55V9vwj35Tx1INPtsGSt4R1c+msFmjevxdXaf7sOa+oXmJDh+UCNDHSF1kQO0g5CVhSRPazjFJ03xIvxttLS0cgihCEkd/x3bv57BOP9rlqE0ANvQNsXoDoyf7+7kKoqcXtiGyCZI7acN2tRasZuWRcAklAnoGKGaYvCWoNL8WJI/8ZFtcShtsQ==","encryptKey":"UTBQyF3S7twtCuiw9XADVZuKL+6iPAkLzho1jrm80H/vlKwojX01YJH316zHAHtZ9hEQbQjFuKo++vjxxWZz8WgAiLXPIpUC4NiKTscVRN7S8plOkaOH62ASv/T0stQG7l/EqEULXXRCyiL0pQjehK6OGQimm7gIFDXtNYL/ReCc8LNpl+e8NQRbmCdMywHWAQtIMigf4ZEPBFtz+1vVb1ETywjsYP6afsfzoMsCXsWD9A1rjWrzlPhlhIYOaUTPyJbtaLHN5Df6Kg5RvEV6KVLO8xpjFBwxsuhl1BmZAyHW02u8PmSpXzVTkXHmp2VeTTQbuMavGUqExdOrsNv1FQ=="},"body":"23Lh8ZnsunlU+eOYc8glY9pxFzHo1KbG+8Pb6nWUGayfMDjuX55WykEfTGOvwFtMUos93iZ38XR05LvYccKPRaXctymgNo6LPfJ+MXtsy2Dp1cvR4lOc4mp17J+uFUjo3EgHq/+63jw0XIQB85/tSc2krhIVSpxA2w/JUvnX5KhiYc8L5O6wnFSKBEkIdAtdJF2EqHEl3zOlwMlZjd8ODe2Mzp8pxpQsX/9sSOUP1pDXz5b1BCcoilF8pHqH3sCACDy/L9R3WzSOUBKIGgJwktrh9XtxliX0qk4PffA+0AWbvg2vMjWLSrP2bEekXWw9o+w1A0HUzVM37ftCzk0OGLKKkFWW2ojpRtIGU87T2xk="}';
		$rows = json_decode($response,true);
		//dump($rows);
		if($rows['head']['respCode'] == "000000" || $notify){
			$key = Tool::rsa_decrypt(base64_decode($rows['head']['encryptKey']));
			$body = json_decode(Tool::AEScode($rows['body'],$key,"DE"),true);
// 			echo "<pre>";
// 			print_r($body);
// 			echo "</pre>";
			if($notify){
				return $body;
			}
			return [
				"status" => 1, 
				"code" => $rows['head']['respCode'], 
				"msg" => $rows['head']['respDesc'],
				"body" => $body
			];
		}else{
			return [
				"status" => 0, 
				"code" => $rows['head']['respCode'], 
				"msg" => $rows['head']['respDesc']
			];
		}
	}
	/**
	 * 公共流程
	 * @param array $params
	 * @param string $method
	 */
	protected function publicHandle($params, $method){
		if(!Tool::isArray($params)){
			throw new MyException("参数不是数组或为空");
		}
		if(empty($method)){
			throw new MyException("提交方法不能为空");
		}
		//dump($params);
		$body = Tool::json_encode_ex([
			"body" => $params
		]);
		//file_put_contents("123",$body . "\n",FILE_APPEND);
		$AES = Tool::AEScode($body);
		//dump($AES);
		//file_put_contents("123","AES秘钥:" . $AES[0] . "\n",FILE_APPEND);
		$RSA = base64_encode(Tool::rsa_encrypt($AES[0]));
		$this->setBody($AES[1]);
		$this->setMainParams($method,Tool::sign($body),$RSA);
		//file_put_contents("123","报文:" . var_export($this->mainParams,true) . "\n",FILE_APPEND);
// 		dump($this->mainParams);
		$response = Tool::curl_json(SER_URL,$this->mainParams);
		return $response;
	}
	/**
	 * 设置报文头部主要参数
	 * @param string $method
	 * @param string $sign
	 * @param string $encrypt
	 * @throws MyException
	 */
	protected function setMainParams($method, $sign, $encrypt){
		if(empty($method) || empty($sign) || empty($encrypt)){
			throw new MyException("方法名,签名,加密串不能为空");
		}
		$this->mainParams['head']['method'] = $method;
		$this->mainParams['head']['sign'] = $sign;
		$this->mainParams['head']['encryptKey'] = $encrypt;
	}
	/**
	 * 设置报文主体
	 * @param string $str
	 * @throws MyException
	 */
	protected function setBody($str){
		if(empty($str)){
			throw new MyException("body不能为空");
		}
		$this->mainParams['body'] = $str;
	}
	/**
	 * 设置参数
	 * @param string $key
	 * @param string $val
	 */
	public function setParams($key, $val){
		$this->body[$key] = $val;
	}
}
/**
 * 商户入网
 * @author Hojk
 */
class BusinessIn extends HPay{
	protected $method = "merchant.join";
	protected $body = [
		"info" => [
				/*
			"merchantNo" => "0000000001", 
			"merchantName" => "内蒙古益付宝信息科技有限公司", 
			"shortName" => "小掌柜", 
			"address" => "内蒙古呼和浩特市赛罕区万达广场B座1405", 
			"serPhone" => "0471-2501261", 
			"category" => "2016062900190190", 
			"idCard" => "150125199304080211", 
			"name" => "郝佳", 
			"phone" => "", 
			"mobile" => "18548144334", 
			"email" => "", 
			"merchantLicense" => "", 
			"remark" => ""
			*/
		], 
		"account" => [
				/*
			"bankCode" => "302191027136", 
			"bankName" => "中信银行股份有限公司呼和浩特东影南路支行", 
			"accountName" => "郝佳", 
			"bankCard" => "6217735600439290", 
			"isRealAccount" => "Y",
			"accountType" => "N"
			*/
		], 
		"payType" => [
				/*
			[
				"payCode" => "WXGZHZF", 
				"withdrawRate" => "0.0008", 
				"tradeRate" => "0.0036", 
				"settleAmt" => "1",
				"withdrawAmt" => "1"
			], 
			[
				"payCode" => "ZFBSMZF", 
				"withdrawRate" => "0.0008", 
				"tradeRate" => "0.0060", 
				"settleAmt" => "1",
				"withdrawAmt" => "1"
			], 
			[
				"payCode" => "WXSMZF", 
				"withdrawRate" => "0.0008", 
				"tradeRate" => "0.0036", 
				"settleAmt" => "1",
				"withdrawAmt" => "1"
			]
			*/
		]
	];
	public function handle(){
		$response = $this->publicHandle($this->body,$this->method);
// 		echo $response;
		return $this->decode($response);
	}
}
/**
 * 商户入网查询
 * @author Hojk
 * TODO: 华付通暂未提供
 */
class BusinessInQuery extends HPay{
	protected $method = "merchant.info.query";
	protected $body = [
		//"payCode" => "ZFBSMZF",  // WXGZHZF WXSMZF
		//"merchantNo" => "847372643006955520"
	];
	public function handle(){
		$response = $this->publicHandle($this->body,$this->method);
		return $this->decode($response);
	}
}
/**
 * 商户入网信息变更
 * @author Hojk
 */
class BusinessInChange extends HPay{
	protected $method = "merchant.info.update";
	protected $body = [
		"" => ""
	];
	public function handle(){
		$response = $this->publicHandle($this->body,$this->method);
		//echo $response;
		return $this->decode($response);
	}
}
/**
 * 钱包查询
 * @author Hojk
 */
class WalletQuery extends HPay{
	protected $method = "merchant.wallet.query";
	protected $body = [
		"merchantNo" => "" 
	];
	public function handle(){
		$response = $this->publicHandle($this->body,$this->method);
		$result = $this->decode($response);
		return $result;
	}
}
/**
 * 结算账户变更 
 * @author Hojk
 */
class CardUpdate extends HPay{
	protected $method = "merchant.card.update";
	protected $body = [
		"merchantNo" => BUS_ID, 
		"mobile" => "18548144334",  //手机号码
		"newBankCard" => "6217735600439290",  //银行卡号
		"newBankCode" => "302191027136",  //开户行代码
		"newBankName" => "中信银行股份有限公司呼和浩特东影南路支行",  //开户行名称
		"returnURL" => ""
	]; //银行卡变更结果异步回调地址
	public function handle(){
		$response = $this->publicHandle($this->body,$this->method);
		echo $response;
		dump($this->decode($response));
	}
}
/**
 * 结算费率变更
 * @author Hojk
 */
class RateUpdate extends HPay{
	protected $method = "merchant.rate.update";
	protected $body = [
		"merchantNo" => "", 
		"payCode" => "", 
		"withdrawRate" => "0", 
		"tradeRate" => "", 
		"settleAmt" => "1",
		"withdrawAmt" => "2"
	];
	public function handle(){
		$response = $this->publicHandle($this->body,$this->method);
		//echo $response;
		return $this->decode($response);
	}
}
/**
 * 扫码支付
 * @author Hojk
 */
class ScanPay extends HPay{
	protected $method = "trade.order";
	protected $body = [
		"merchantNo" => "", 
		"userOrderNo" => "", 
		"payCode" => "", 
		"orderAmt" => "", 
		"orderTitle" => "小掌柜-秒结", 
		"remark" => "小掌柜-秒结", 
		"notifyUrl" => "", 
		"returnUrl" => ""
	];
	public function handle(){
		if($this->body['orderAmt'] == "" || $this->body['orderAmt'] == "0" || !is_numeric($this->body['orderAmt'])){
			throw new MyException("金额非法");
		}
		//$this->body['userOrderNo'] = Tool::GO("SMZF");
		//$this->body['notifyUrl'] = P_URL . "/EasyApp/Test/notifyUrl";
		//$this->body['returnUrl'] = P_URL . "/EasyApp/Test/returnUrl";
		$response = $this->publicHandle($this->body,$this->method);
		//echo $response;
		return $this->decode($response);
	}
}
/**
 * 公众号支付 
 * @author Hojk
 */
class PublicNoPay extends HPay{
	protected $method = "trade.order.official";
	protected $body = [
		"merchantNo" => "", 
		"userOrderNo" => "", 
		"payCode" => "WXGZHZF", 
		"orderAmt" => "", 
		"returnType" => "02", 
		//"appId" => "", 
		//"openId" => "", 
		"orderTitle" => "小掌柜", 
		//"orderDesc" => "xzg",
		//"opertorId" => "0001",
		//"storeId" => "",
		//"terminalId" => "",
		//"limitPay" => "",
		//"remark" => "",
		//"ip" => "",
		//"appNo" => "",
		"notifyUrl" => "", 
		"returnUrl" => ""
	];
	public function handle(){
		if($this->body['orderAmt'] == "" || $this->body['orderAmt'] == "0" || !is_numeric($this->body['orderAmt'])){
			throw new MyException("金额非法");
		}
		$this->setParams("userOrderNo",Tool::GO("GZHZF"));
// 		$this->setParams("orderAmt","1");
// 		$this->setParams("merchantNo","833610537329102848");
// 		$this->setParams("notifyUrl",P_URL . "/index.php/EasyApp/ChinaTenpay/pnpNotifyUrl");
// 		$this->setParams("returnURL",P_URL . "/index.php/EasyApp/ChinaTenpay/pnpReturnUrl");
		$response = $this->publicHandle($this->body,$this->method);
		return $this->decode($response);
	}
}
/**
 * 订单查询
 * @author Hojk
 */
class OrderQuery extends HPay{
	protected $method = "trade.query";
	protected $body = [
		"merchantNo" => BUS_ID,
		"merchantNo" => "",
		"orderNo" => "",
	];
	public function handle(){
		$response = $this->publicHandle($this->body,$this->method);
		$result = $this->decode($response);
		dump($result);
	}
}
/**
 * 获取对账文件
 * @author Hojk
 */
class FileGet extends HPay{
	protected $method = "channel.file.get";
	protected $body = [
		"billDate" => "2016-09-26"
	];
	public function handle(){
		$response = $this->publicHandle($this->body,$this->method);
		$result = $this->decode($response);
		dump($result);
	}
}
/**
 * 商户提现
 * @author Hojk
 */
class BusinessWithdrawal extends HPay{
	protected $method = "merchant.withdrawals";
	protected $body = [
		"merchantNo" => "", 
		"userOrderNo" => "", 
		"payCode" => "", 
		"amount" => "", 
		"remark" => "提现", 
		"returnURL" => ""
	];
	public function handle(){
// 		$this->setParams("userOrderNo",Tool::GO("TX"));
// 		$this->setParams("amount","10");
		//$this->setParams("returnURL",P_URL . C_F . "asdf");
		//dump($this->body);
		$response = $this->publicHandle($this->body,$this->method);
		//echo $response;
		//dump($this->decode($response));
		return $this->decode($response);
	}
}
/**
 * 商户提现查询
 * @author Hojk
 */
class BusinessWithdrawalQuery extends HPay{
	protected $method = "merchant.draw.query";
	protected $body = [
		"merchantNo" => BUS_ID, 
		"userOrderNo" => "YDSM_SMZF_20170207112759mdwq69", 
		"payCode" => "ZFBSMZF", 
		"amount" => "1000", 
		"remark" => "提现", 
		"returnURL" => ""
	];
	public function handle(){
// 		$this->setParams("userOrderNo",Tool::GO("TX"));
// 		$this->setParams("amount","10");
		$this->setParams("returnURL",P_URL . C_F . "asdf");
		dump($this->body);
		$response = $this->publicHandle($this->body,$this->method);
		echo $response;
		dump($this->decode($response));
	}
}
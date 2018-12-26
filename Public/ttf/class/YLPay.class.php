<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2017年2月22日 上午9:23:57
# Filename: NewCBank.class.php
# Description: 央联支付 新接口
#================================================
include_once ("Tool.class.php");
include_once ("HashMap.class.php");
define("DOMAINURL","http://pay.xylrcs.cn/index.php");
define("SERVER","http://v.bank-pay.com.cn/");
define("MER_ID","C100517");
define("KEY","b6e75682de427bfc9aa25bcae660a557");
define("RSA","MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCJiSNJaNzCdkNaOkMWXTGkZ4h1Sn7i+XIPdq9AM65GzYa6a7Gimu32wZrENS9WCyNwiSs6AM+FtIEa6+fawM2zP2Wlho1OeqOswfM48Q72h8stzp7FZFbY0t9KKA3hhX5BRlORKV2FOgDnpgdfuyiWuXaM1n6iM+Jbri+I2UzrmwIDAQAB");
class YLPay{
	/**
	 * 设置参数
	 * @param string $key
	 * @param string $val
	 */
	public function setParams($key, $val){
		$this->params[$key] = trim($val);
	}
	public function setExtends($key, $val){
		$this->extends[$key] = trim($val);
	}
	/**
	 * 获取参数
	 * @param string $key
	 */
	public function getParams($key){
		return $this->params[$key];
	}
	public function getExtends($key){
		return $this->extends[$key];
	}
	/**
	 * 央联代付响应码解密
	 * @param array $response
	 */
	public function decode($response){
		return json_decode(Tool::rsa_pubdecrypt(base64_decode($response['cipherText'])),true);
	}
	/**
	 * 参数hashmap
	 * @param array $params
	 */
	protected function getMap($params){
		if(is_array($params) && count($params) > 0){
			$hashMap = new HashMap();
			foreach($params as $key => $val){
				$hashMap->put($key,$val);
			}
			return $hashMap->toString();
		}
		return false;
	}
}
/**
 * 支付 
 */
class Pay extends YLPay{
	protected $method = "gateway/interface/pay.htm";
	protected $ylMethod = "gateway/pay.htm";
	protected $params = [
		"apiCode" => "YL-PAY",  //接口编码
		"inputCharset" => "UTF-8",  //字符集
		"signType" => "MD5",  //签名方式
		"partner" => MER_ID,  //商户编号
		"outOrderId" => "",  //商户订单号
		"product" => "小掌柜",  //商品名称
		"amount" => "",  //订单金额
		//"extParam" => "",		//业务拓展参数
		"returnParam" => "",  //returnParam	
		//"interfaceCode" => "",		//接口交易编号
		"payType" => "",  //支付类型 1、微信扫码 ALIPAY 2、支付宝扫码 WXNATIVE 3、认证支付 AUTHPAY 
		//"bankCardNo" => "",	//银行卡号	
		//"Sign" => "",		//签名摘要
		"notifyUrl" => ""
	]; //通知地址
	protected function getForm(){
		$form = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><script>window.onload=function(){document.getElementById('myForm').click();}</script><form action='" . SERVER . $this->ylMethod . "' method='post'>";
		foreach($this->params as $key => $val){
			$form .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
		}
		$form .= "<input type='submit' style='display:none' id='myForm'></form>";
		return $form;
	}
	/**
	 * 设置支付类型
	 * @param string $type wx zfb yl
	 * @param string $orderNum
	 * @return false|订单号
	 */
	public function setPayType($type, $orderNum = ""){
		switch($type){
			case "wx":
				$payType = "WXNATIVE";
				$orderNo = "SMb";
				break;
			case "zfb":
				$payType = "ALIPAY";
				$orderNo = "SMbZFB";
				break;
			case "yl":
				$payType = "AUTHPAY";
				$orderNo = "YLb";
				break;
			default:
				return false;
				break;
		}
		if(empty($orderNum)){
			$orderNum = "YDSMs" . $orderNo . time() . rand(1,100);
		}
		$this->setParams("payType",$payType);
		$this->setParams("outOrderId",$orderNum);
	}
	/**
	 * 公共支付流程
	 * @param int $bid
	 * @param decimal $money
	 * @param string $type
	 * @throws CBkException
	 */
	public function handle(){
		if(!is_numeric($this->params["amount"]) || $this->params["amount"] < 1){
			throw new MyException("金额不正确,请填写1元以上的数值");
		}
		if(empty($this->params["payType"])){
			throw new MyException("支付方式错误");
		}
		if(empty($this->params["notifyUrl"])){
			throw new MyException("通知地址不能为空");
		}
		if($this->params["payType"] == "AUTHPAY"){
			$this->setParams("interfaceCode","AUTHPAY_YLZF-DEBIT-CARD");
			$this->setParams("submitTime",date("YmdHis"));
		}
		$sign = Tool::GS($this->params,"SP");
		$this->setParams("sign",$sign);
		if($this->params["payType"] == "AUTHPAY"){
			return $this->getForm();
		}else{
			$sendParams = $this->getMap($this->params);
			$response = json_decode(Tool::curl_json(SERVER . $this->method,$sendParams),true);
			if($response['responseCode'] == "0000"){
				$status = [
					"status" => 1, 
					"ordernum" => $response['outOrderId'], 
					"url" => $response['qrCodeUrl']
				];
			}else{
				$status = [
					"status" => 0, 
					"msg" => $response['responseMsg']
				];
			}
			return $status;
		}
	}
}
/**
 * 交易查询
 */
class PayQuery extends YLPay{
	protected $method = "gateway/query.htm";
	protected $params = [
		"queryCode" => "YL-QUERY", 
		"inputCharset" => "UTF-8", 
		"partner" => MER_ID, 
		"outOrderId" => "", 
		"signType" => "MD5"
	];
	//"sign" => "",
	public function handle(){
		$this->setParams("outOrderId","YDSMsSMb14878383111");
		$sign = Tool::GS($this->params,"SP");
		$this->setParams("sign",$sign);
		$sendParams = $this->getMap($this->params);
		dump($sendParams);
		$response = json_decode(Tool::curl_json(SERVER . $this->method,$sendParams),true);
		dump($response);
	}
}
/**
 * 代付 
 */
class Paid extends YLPay{
	protected $method = "dpay-front/dpayTrade";
	protected $params = [
		"customerNo" => MER_ID, 
		"versionCode" => "1.0", 
		"customerParam" => "", 
		"customerRequestTime" => "", 
		"cipherText" => ""
	];
	protected $extends = [
		"cutomerOrderCode" => "", 
		"accountNo" => "", 
		"accountName" => "", 
		"amount" => "", 
		"bankName" => "", 
		"accountType" => "INDIVIDUAL", 
		"cardType" => "DEBIT", 
		// 	"validity" => "0826",
		// 	"cvv" => "441",
		"cerType" => "ID", 
		"cerNo" => "", 
		"description" => "提现", 
		"notifyUrl" => ""
	];
	public function handle(){
		foreach($this->extends as $key => $val){
			if(empty($val)){
				throw new MyException("扩充参数" . $key . "不能为空");
			}
		}
		$this->setParams("customerRequestTime",date("YmdHis"));
		$this->setParams("cipherText",Tool::pubencrypt($this->extends));
		$sendParams = $this->getMap($this->params);
		$response = json_decode(Tool::curl_json(SERVER . $this->method,$sendParams),true);
		$result = $this->decode($response);
		switch($result['responseCode']){
			//代付成功
			case "1000":
				$code = 1;
				break;
			//代付失败
			case "1001":
				$code = 3;
				break;
			//下单成功，待审核
			case "0000":
				$code = 2;
				break;
			//下单成功，处理中
			case "0100":
				$code = 2;
				break;
			default:
				$code = 0;
				break;
		}
		$status = [
			"status" => $code, 
			"msg" => $result['responseMsg']
		];
		return $status;
	}
}
/**
 * 代付查询
 */
class PaidQuery extends YLPay{
	protected $method = "dpay-front/dpayQuery";
	protected $params = [
		"customerNo" => MER_ID, 
		"cipherText" => ""
	];
	protected $extends = [
		"customerOrderCode" => ""
	];
	public function handle(){
		if(empty($this->extends["customerOrderCode"])){
			throw new MyException("订单号不能为空");
		}
		$this->setParams("cipherText",Tool::pubencrypt($this->extends));
		$sendParams = $this->getMap($this->params);
		$response = json_decode(Tool::curl_json(SERVER . $this->method,$sendParams),true);
		$result = $this->decode($response);
		switch($result['responseCode']){
			//代付成功
			case "1000":
				$code = 1;
				break;
			//代付失败
			case "1001":
				$code = 3;
				break;
			//受理成功，待审核
			case "0000":
				$code = 2;
				break;
			default:
				$code = 0;
				break;
		}
		$status = [
			"status" => $code, 
			"msg" => $result['responseMsg']
		];
		return $status;
	}
}
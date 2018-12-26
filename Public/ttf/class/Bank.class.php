<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2016年8月28日 上午10:06:56
# Filename: Bank.class.php
# Description: 提现到银行卡处理类
#================================================
define("MER_ID","10083289891");
define("MER_IP","101.201.53.133");
define("SER_URL","https://www.99bill.com/webapp/services/BatchPayWS?wsdl");
define("KEY","DDNLE7JLJE29HKGL");
class Bank{
	private $name = "";
	private $amount = 0;
	private $orderId = "";
	private $bankName = "";
	private $bankCardNum = "";
	//设置金额
	public function setAmount($val){
		$this->amount = $val;
	}
	//设置持卡人姓名
	public function setName($val){
		$this->name = $val;
	}
	//设置银行名称
	public function setBankName($val){
		$this->bankName = $val;
	}
	//设置银行卡号
	public function setBankCardNum($val){
		$this->bankCardNum = $val;
	}
	/**
	 * 获取订单ID
	 * @return string
	 */
	public function getOrderId($ordernum = ""){
		if(empty($this->orderId)){
			if(empty($ordernum)){
				$this->orderId = "YDSM_BK_" . time() . rand(1,1000);
			}else{
				$this->orderId = $ordernum;
			}
		}
		return $this->orderId;
	}
	/**
	 * 获得签名
	 * @return string
	 */
	protected function getSign(){
		$params = $this->bankCardNum . $this->amount . $this->orderId . KEY;
		return strtoupper(md5($params));
	}
	/**
	 * 银行卡付款
	 * @param string $bankName
	 * @param string $name
	 */
	public function bankPay(){
		if(empty($this->bankName) || empty($this->name) || empty($this->bankCardNum)){
			throw new BkException("银行名称与银行卡号与持卡人姓名不能为空");
		}
		if(!is_numeric($this->bankCardNum) || $this->amount <= 0){
			throw new BkException("金额不正确,请填写1元以上的数值");
		}
		//$this->getOrderId();
		$para = array();
		$para['province_city'] = "呼和浩特";
		$para['bankName'] = $this->bankName;
		$para['kaihuhang'] = "开户行";
		$para['creditName'] = $this->name;
		$para['bankCardNumber'] = $this->bankCardNum;
		$para['amount'] = $this->amount;
		$para['description'] = "小掌柜账户提现";
		$para['orderId'] = $this->orderId;
		//获得签名
		$para['mac'] = $this->getSign();
		//开始处理数据
		libxml_disable_entity_loader(false); //处理间歇性报错问题
		$clientObj = new SoapClient(SER_URL);
		try{
			$result = $clientObj->__soapCall("bankPay",array(
				array(
					$para
				), 
				MER_ID, 
				MER_IP
			));
			$response = $this->object_array($result);
			return $response[0];
		}catch(SoapFault $e){
			return $e;
		}
	}
	/**
	 * 查询订单
	 * @param string $orderId       订单号 dealId 为0时填写
	 * @param string $dealBeginDate 开始时间 格式 yyyy-mm-dd HI:MM:SS
	 * @param string $dealEndDate   结束时间 格式 yyyy-mm-dd HI:MM:SS
	 * @param number $dealId        交易号 0 商户订单号查询 1 交易时间查询
	 * @param string $queryType     查询类型 simplePay 代表付款到快钱账户 bankPay 代表付款到银行账户
	 */
	public function queryDeal($orderId = "", $dealBeginDate = "", $dealEndDate = "", $dealId = 0, $queryType = "bankPay"){
		$para = array();
		$para['dealBeginDate'] = $dealBeginDate;
		$para['dealEndDate'] = $dealEndDate;
		$para['dealId'] = $dealId;
		$para['queryType'] = $queryType;
		$para['orderId'] = $orderId;
		libxml_disable_entity_loader(false);
		$clientObj = new SoapClient(SER_URL);
		try{
			$result = $clientObj->__soapCall("queryDeal",array(
				$para, 
				MER_ID, 
				MER_IP
			));
			$response = $this->object_array($result);
			if($response){
				return [
					"status" => 1, 
					"data" => $response[0]
				];
			}else{
				return [
					"status" => 0, 
					"msg" => "没有找到该订单"
				];
			}
		}catch(SoapFault $e){
			return [
				"status" => 0, 
				"msg" => "相同订单号请求太频繁"
			];
		}
	}
	public function object_array($array){
		if(is_object($array)){
			$array = (array)$array;
		}
		if(is_array($array)){
			foreach($array as $key => $value){
				$array[$key] = $this->object_array($value);
			}
		}
		return $array;
	}
}
//异常类
class BkException extends Exception{
	public function errorMessage(){
		return $this->getMessage();
	}
}
?>
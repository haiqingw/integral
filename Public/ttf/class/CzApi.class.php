<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2016年10月26日 上午10:28:02
# Filename: CzApi.class.php
# Description: 充值Api 
#================================================
include_once ("Tool.class.php");
define("USER","13600000477");
define("KEY","CAD0E56F4F17C08E4FE91734B686CBB9");
define("QUERY_URL","http://dis.diwudai.com/pages/Interface/TradeForOut/getTrade/index.asp");
define("RECHARGE_URL","http://dis.diwudai.com/pages/Interface/TradeForOut/setTrade/index.asp");
define("CHECK_RECHARGE_URL","http://dis.diwudai.com/pages/Interface/TradeForOut/getP.asp");
class CzApi{
	/**
	 * 获取第五代签名
	 * @param string $value
	 */
	protected function getSign($value){
		if(empty($value)){
			throw new MyException("签名参数不能为空");
		}
		$sign = md5(strtolower(md5(USER) . md5(KEY) . md5(KEY . $value)));
		return $sign;
	}
	/**
	 * 设置参数
	 * @param string $key
	 * @param string $val
	 */
	public function setParams($key, $val){
		$this->params[$key] = $val;
	}
	/**
	 * 获取参数
	 * @param string $key
	 */
	public function getParams($key){
		return $this->params[$key];
	}
}
/**
 * 验证是否可以充值
 * @date: 2016年10月26日 上午11:04:45
 * @author: hojk - hojk@foxmail.com
 * @param: variable
 * @return:
 */
class CheckRecharge extends CzApi{
	protected $url = CHECK_RECHARGE_URL;
	protected $params = [
		"uid" => USER, 
		"rnd" => "", 
		"mianzhi" => "", 
		"account" => "", 
		"chargeType" => 1, 
		"returnType" => "JSON", 
		"sign" => ""
	];
	public function handle(){
		$rand = mt_rand(1000,9999);
		$this->setParams("rnd",$rand);
		$this->setParams("sign",$this->getSign($rand));
		$response = Tool::curl_request($this->url . Tool::arrayToget($this->params));
		return json_decode($response,true);
	}
}
/**
 * 充值
 * @date: 2016年10月26日 上午11:05:11
 * @author: hojk - hojk@foxmail.com
 * @param: $GLOBALS
 * @return:
 */
class Recharge extends CzApi{
	protected $url = RECHARGE_URL;
	protected $params = [
		"uid" => USER,  //第五代代理账号用户名
		"orderid" => "",  //商家订单号 多个单号用$分割
		"sn" => "", 
		"mianzhi" => 100, 
		"account" => "15847759480", 
		"account2" => "", 
		"num" => "", 
		"chargeType" => 1,  //1(手机充值),2(游戏充值),3(固话)
		"returnType" => "JSON", 
		"sign" => "", 
		"corp" => "",  //LT(联通固话),DX(电信固话)
		"uidtype" => 1
	];
	public function handle(){
		$response = Tool::curl_request($this->url . Tool::arrayToget($this->params));
		return json_decode($response,true);
	}
}
/**
 * 订单查询
 * @date: 2016年10月26日 上午11:05:26
 * @author: hojk - hojk@foxmail.com
 * @param: $GLOBALS
 * @return:
 */
class QueryOrder extends CzApi{
	protected $url = QUERY_URL;
	protected $params = [
		"Uid" => USER, 
		"Orderids" => "",  //商家订单号 多个单号用$分割
		"type" => "JSON", 
		"sign" => ""
	];
	public function handle(){
		$response = Tool::curl_request($this->url . Tool::arrayToget($this->params));
		return json_decode($response,true);
	}
}
?>
<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2018年4月16日 下午1:42:41
# Filename: WxController.class.php
# Description: 
#================================================
namespace App\Controller;
use Think\Controller;
class WxController extends BaseController{
	protected function api(){
		importTTF("WxApi.class.php");
		return new \WxApi();
	}
	protected function apiSP(){
		importTTF("WxApiSPOS.class.php");
		return new \WxApi();
	}
	/**
	 * 获取session_key
	 * @param unknown $params
	 * @return unknown
	 */
	public function getsessionkey($params){
		$code = trim($params['code']);
		$response = $this->api()->getSessionKey($code);
		return $response;
	}
	/**
	 * 获取session_key
	 * @param unknown $params
	 * @return unknown
	 */
	public function getsessionkeysp($params){
		$code = trim($params['code']);
		$response = $this->apiSP()->getSessionKey($code);
		return $response;
	}
	public function salesmanrecharge($params){
// 		$ordernum = "SR" . date('YmdHis') . mt_rand(100, 999);
		$ordernum = $params['ordernum'];
		$money = $params['money'];
		$openid = $params['openid'];
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if(parent::check($phone, $bid, $pid)){
			$bid = parent::busID(array("phone" => $phone,"plat" => $pid));
			$data = $this->createunifiedorder($ordernum, $money, $openid, $bid, "http://ttsplus.xylrcs.cn/index.php/App/Wx/salesmanRechargeNotify");
			$ret = array("responseStatus" => 1,"data" => $data);
		}else{
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	public function salesmanrechargesp($params){
		// 		$ordernum = "SR" . date('YmdHis') . mt_rand(100, 999);
		$ordernum = $params['ordernum'];
		$money = $params['money'];
		$openid = $params['openid'];
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if(parent::check($phone, $bid, $pid)){
			$bid = parent::busID(array("phone" => $phone,"plat" => $pid));
			$data = $this->createunifiedorderSP($ordernum, $money, $openid, $bid, "http://ttsplus.xylrcs.cn/index.php/App/Wx/salesmanRechargeNotify");
			$ret = array("responseStatus" => 1,"data" => $data);
		}else{
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	public function createunifiedorder($ordernum, $money, $openid, $bid, $notify = "http://ttsplus.xylrcs.cn/index.php/App/Wx/notify"){
		$api = $this->api();
		$api->setParams("body", "刷多宝"); //商品描述
		$api->setParams("out_trade_no", $ordernum); //订单号
		$api->setParams("total_fee", $money * 100); //金额 单位：分
		$api->setParams("openid", $openid); //openid 前台传输
		$api->setParams("attach", $bid); //附加内容，回调原样返回
		$api->setParams("notify_url", $notify);
		$api->setParams("trade_type", "JSAPI");
		//$api->setParams("limit_pay", "no_credit");	//限制使用信用卡
		$response = $api->unifiedorder();
		return $response;
	}
	public function createunifiedorderSP($ordernum, $money, $openid, $bid, $notify = "http://ttsplus.xylrcs.cn/index.php/App/Wx/notify"){
		$api = $this->apiSP();
		$api->setParams("body", "闪POS"); //商品描述
		$api->setParams("out_trade_no", $ordernum); //订单号
		$api->setParams("total_fee", $money * 100); //金额 单位：分
		$api->setParams("openid", $openid); //openid 前台传输
		$api->setParams("attach", $bid); //附加内容，回调原样返回
		$api->setParams("notify_url", $notify);
		$api->setParams("trade_type", "JSAPI");
		//$api->setParams("limit_pay", "no_credit");	//限制使用信用卡
		$response = $api->unifiedorder();
		return $response;
	}
	public function salesmanRechargeNotify(){
		importTTF('WxApi.class.php');
		importTTF('log.php');
		//初始化日志
		$logHandler = new \CLogFileHandler("./Uploads/logs/" . date('Y-m-d') . '.log');
		$log = \Log::Init($logHandler, 15);
		\Log::DEBUG("begin notify");
		//实例化回调类
		$notify = new \PayNotifyCallBack();
		//获取通知数据
		$result = $notify->Handle();
		//判断支付是否成功
		if($result == false){
			//==================支付失败===================
		}else{
			//==================支付成功 流程开始===================
			// 这里是业务员充值 支付成功流程
			$dao = A("Servicefee");
			$dao->servicePay($result);
			//==================支付成功 流程结束===================
		}
	}
	public function notify(){
		importTTF('WxApi.class.php');
		importTTF('log.php');
		//初始化日志
		$logHandler = new \CLogFileHandler("./Uploads/logs/" . date('Y-m-d') . '.log');
		$log = \Log::Init($logHandler, 15);
		\Log::DEBUG("begin notify");
		//实例化回调类
		$notify = new \PayNotifyCallBack();
		//获取通知数据
		$result = $notify->Handle();
		//判断支付是否成功
		if($result == false){
			//==================支付失败===================
		}else{
			// 			file_put_contents("./notify",var_export($result,true));
			// 			parent::aLogs("wxPay",$result,$result['attach']);
			$dao = A("Order");
			$dao->orderPay($result);
			// 			$data = array("isPay" => 1,"isDeposit" => 1,"wxIsPay" => 2);
			//==================支付成功 流程开始===================
			//==================支付成功 流程结束===================
		}
	}
}
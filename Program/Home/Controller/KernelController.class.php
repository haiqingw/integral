<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2017年3月13日 下午4:42:19
# Filename: KernelController.class.php
# Description: 核心控制器，交易&提现
#================================================
namespace Home\Controller;
use Think\Controller;
class KernelController extends BaseController{
	/**
	 * 调用基础类公共方法
	 * @param string $className
	 */
	protected function YLPay($className){
		importTTF("YLPay.class.php");
		if(class_exists($className)){
			$class = new $className();
			return $class;
		}
		return false;
	}
	
	
	//获取银行卡信息
	private static function getBankInfo($bid, $id){
		$info = fRec(T_BNK,'id=' . $id . '||bid=' . $bid . '||status=1','id,card_number,name,bank_name');
		$row = fRec(T_BUS,"id=" . $bid,"idCard,realName");
		if($info){
			$bank = @explode("-",$info['bank_name']);
			return [
				'id' => $info['id'], 
				'name' => $row['realName'], 
				'idCard' => $row['idCard'], 
				'bankName' => $bank[0], 
				'bankCardNum' => $info['card_number'], 
				'wh' => substr($info['card_number'],strlen($info['card_number']) - 3)
			];
		}
		return false;
	}
	
	/**
	 * 小掌柜提现
	 * @param number $bid
	 * @param decimal $money
	 * @param string $ordernum
	 * @param array $bankInfo
	 */
	public function easyPaid($bid, $money, $ordernum, $bankInfo){
		//代付参数
		$daiFuParams = array(
			"ordernum" => $ordernum, 
			"cardnum" => $bankInfo['bankCardNum'], 
			"name" => $bankInfo['name'], 
			"money" => $money, 
			"bankname" => $bankInfo['bankName'], 
			"idcard" => $bankInfo['idCard'], 
			"bid" => $bid
		);
		foreach($daiFuParams as $key => $val){
			if(empty($val)){
				return [
					"status" => 0, 
					"msg" => "提现参数" . $key . "不能为空"
				];
			}
		}
		return $this->callDaiFu($daiFuParams);
	}
	/**
	 * 审核提现
	 */
	public function auditPaid(){
		$data = I("post.data");
		$data = json_decode($data,true);
		if(count($data) < 0 || !is_array($data)){
			return false;
		}else{
			$returnArr = array();
// 			$i = 0;
// 			foreach($data as $val){
// 				if(!empty($val['bid']) && !empty($val['ordernum']) && !empty($val['money']) && !empty($val['bankid'])){
// 					$bankInfo = self::getBankInfo($val['bid'],$val['bankid']);
// 					if($bankInfo){
// 						$res = $this->easyPaid($val['bid'],$val['money'],$val['ordernum'],$bankInfo);
// 						file_put_contents("./Uploads/App/audit.txt","[" . date('Y-m-d H:i:s') . "]" . var_export($res,true) . "\n",FILE_APPEND);
// 						$returnArr[$i] = $res;
// 					}else{
// 						$returnArr[$i] = [
// 							'status' => 0, 
// 							'msg' => '银行卡未找到'
// 						];
// 					}
// 				}else{
// 					$returnArr[$i] = [
// 						'status' => 0, 
// 						'msg' => '有参数为空'
// 					];
// 				}
// 				$returnArr[$i]['bid'] = $val['bid'];
// 				$returnArr[$i]['ordernum'] = $val['ordernum'];
// 				$returnArr[$i]['money'] = $val['money'];
// 				$i++;
// 			}
			if(!empty($data['bid']) && !empty($data['ordernum']) && !empty($data['money']) && !empty($data['bankid'])){
				$bankInfo = self::getBankInfo($data['bid'],$data['bankid']);
				if($bankInfo){
					$res = $this->easyPaid($data['bid'],$data['money'],$data['ordernum'],$bankInfo);
					file_put_contents("./Uploads/App/audit.txt","[" . date('Y-m-d H:i:s') . "]" . var_export($res,true) . "\n",FILE_APPEND);
					$returnArr = $res;
				}else{
					$returnArr = [
						'status' => 0, 
						'msg' => '银行卡未找到'
					];
				}
			}else{
				$returnArr = [
					'status' => 0, 
					'msg' => '有参数为空'
				];
			}
			$returnArr['bid'] = $data['bid'];
			$returnArr['ordernum'] = $data['ordernum'];
			$returnArr['money'] = $data['money'];
			echo json_encode($returnArr);
		}
	}
	/**
	 * 后台查询订单号
	 */
	public function adminPaidQuery(){
		$ordernum = I("post.ordernum");
		if(empty($ordernum)){
			$status = array(
				"status" => "0", 
				"msg" => "订单号不能为空"
			);
		}else{
			$status = $this->paidQuery($ordernum);
		}
		echo json_encode($status);
	}
	/**
	 * 调用代付接口
	 * @param array $params
	 */
	protected function callDaiFu($params){
		if(self::hasDaiFuOrder($params['bid'],$params['ordernum'])){
			$res = $this->paidQuery($params['ordernum']);
			if(!$res['status']){
				$class = $this->YLPay("Paid");
				$class->setExtends("cutomerOrderCode",$params['ordernum']);
				$class->setExtends("accountNo",$params['cardnum']);
				$class->setExtends("accountName",$params['name']);
				$class->setExtends("amount",$params['money']);
				$class->setExtends("bankName",$params['bankname']);
				$class->setExtends("cerNo",$params['idcard']);
				$class->setExtends("notifyUrl",BASEURL . "/index.php/Home/Kernel/drawcashNotifyUrl");
				$class->setParams("customerParam",$params['bid']);
				$response = $class->handle();
				\Tool::aLogs($response,"xzgPaid");
				switch($response['status']){
					case 1:
						$type = 3;
						break;
					case 2:
						$type = 2;
						break;
					case 3:
						$type = 4;
						break;
					default:
						$type = 4;
						break;
				}
				$this->changeOrderStatus($params['bid'],$params['ordernum'],$type);
				return $response;
			}
			return $res;
		}
		return [
			"status" => 0, 
			"msg" => "未找到该订单"
		];
	}
	public function drawcashNotifyUrl(){
		ob_clean();
		$response = file_get_contents('php://input','r');
        //file_put_contents("1234123123123",$response);
		// 		$response = '{"customerParam":"10","versionCode":"1.0","cipherText":"igCnn7+sWretMtafwTEPhJrWC2vBVabNLx80RiZ8BMxOjXOryMgnGuZQYD84WoWgSGJDWz6MjVZ9\\nJMIbGxSrTNea781gtesiq+f9JKp5GaygSsYcwTaBM+WfEqlpS8fIRvE2Z4ElF/6A6zfRpNVL7L6X\\nUzEhXZxp0XaiGKkwmYCzWrRM1s00ra5Kfo7PJKFtycjZkx496cYO+V5h4wcTgpkMMZ47jJxaTS0I\\nOvrbVkeMLK5vpBBACeKj5+b5Ekb6C69HNYcmBIWxn7HpyMwP2qert5krfew4FmgNOVN1bEnwLj9T\\norruDq+/29YMyTNPksgrEgSsylV/JYBCxJgRcA==","cutomerNo":"C100006"}';
		$response = json_decode($response,true);
		if(empty($response) || !is_array($response) || count($response) <= 0){
			die();
		}else{
			$pay = $this->YLPay("YLPay");
			$result = $pay->decode($response);
			if($result){
				\Tool::aLogs($result,"xzgPaid");
				$money = $result['amount'];
				$poundage = $result['fee'];
				$ordernum = $result['customerOrderId'];
				$bid = $this->getBidOfDaiFuOrder($ordernum);
				if($result['responseCode'] == "1000"){
					//代付成功
					adLog($bid,"代付订单号:" . $ordernum . ",金额：" . $money . ",手续费：" . $poundage . ",提现成功",true);
					$this->changeOrderStatus($bid,$ordernum,3);
					//$ts = "您的提现已到账，秒到时间:9:00 - 19:00,如不在该时间内,系统将在第二天12:00前自动打款！ ";
					//$json = '{"money":"' . $money . '","msg":"' . $ts . '","so":"' . $ordernum . '","atime":"' . dateFormat(time(),4) . '","ctime":"' . dateFormat(time(),4) . '"}';
					//JpushSend("您的提现已到账",md5($bid),"drawcash",$json);
					// 					parent::addMsg($bid,"提现进度更新提醒",$ts,1,1,$ordernum);
				}elseif($result['responseCode'] == "1001"){
					//代付失败
					adLog($bid,"代付订单号:" . $ordernum . ",金额：" . $money . ",手续费：" . $poundage . ",原因：" . $result['responseMsg'] . "提现失败",false);
					$this->changeOrderStatus($bid,$ordernum,4);
				}
				exit("SUCCESS");
			}else{
				die();
			}
		}
	}
	/**
	 * 添加代付订单
	 * @param number $bid
	 * @param decimal $money
	 * @param int $paytime
	 * @param string $ordernum
	 * @param string $ordernumPay
	 * @param number $bankid
	 * @param decimal $poundage
	 */
	public function addRecordsOrder($bid, $money, $ordernum, $bankid, $poundage){
		$data = array(
			"real_time_payment_records_bid" => $bid, 
			"real_time_payment_records_money" => $money, 
			"real_time_payment_records_addtime" => time(), 
			"real_time_payment_records_ordernum" => $ordernum, 
			"real_time_payment_records_bankID" => $bankid, 
			"real_time_payment_records_poundage" => $poundage
		);
		if(!self::hasDaiFuOrder($bid,$ordernum)){
			if(aRec(T_RTPRE,$data)){
				adLog($bid,"代付订单号:" . $ordernum . ",添加记录成功(0)",true);
			}
		}
	}
	protected function getBidOfDaiFuOrder($ordernum){
		$row = fRec(T_RTPRE,"real_time_payment_records_ordernum=" . $ordernum,"real_time_payment_records_bid bid");
		return $row['bid'];
	}
	/**
	 * 是否存在代付订单
	 * @param number $bid
	 * @param string $ordernum
	 */
	protected static function hasDaiFuOrder($bid, $ordernum){
		$where = array(
			"real_time_payment_records_bid" => $bid, 
			"real_time_payment_records_ordernum" => $ordernum
		);
		if(cRec(T_RTPRE,$where)){
			return true;
		}
		return false;
	}
	/**
	 * 改变订单状态
	 * @param number $bid
	 * @param string $ordernum
	 * @param string $type 0 已扣除余额,1 审核中,2 受理中,3 已付款,4 付款失败
	 */
	public function changeOrderStatus($bid, $ordernum, $type){
		$where = array(
			"real_time_payment_records_bid" => $bid, 
			"real_time_payment_records_ordernum" => $ordernum
		);
		$data = array(
			"real_time_payment_records_status" => $type, 
			"real_time_payment_records_paytime" => time()
		);
		if(cRec(T_RTPRE,$where)){
			if(uRec(T_RTPRE,$data,$where)){
				adLog($bid,"代付订单号:" . $ordernum . ",状态修改成功(" . $type . ")",true);
			}
		}
	}
	/**
	 * 代付查询
	 * @param string $ordernum
	 */
	protected function paidQuery($ordernum){
		$class = $this->YLPay("PaidQuery");
		$class->setExtends("customerOrderCode",$ordernum);
		$response = $class->handle();
		return $response;
	}
}

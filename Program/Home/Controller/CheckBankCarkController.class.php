<?php
namespace Home\Controller;
use Think\Controller;
/**
 * +----------------------------------------------------------------------
 * | 银行卡信息验证
 * +----------------------------------------------------------------------
 * | @author HaiQing.Wu <398994668@qq.com>
 * +----------------------------------------------------------------------
 * | last Time : 2016/09/26
 * +----------------------------------------------------------------------
 */
define("VERIFY_URL","http://apis.haoservice.com/creditop/BankCardQuery/QryBankCardBy4Element");
define("VERIFY_KEY","d067e3a7760844be974cb8e10720b540");
define("T_FA","four_auth");
class CheckBankCarkController extends CommonController{
	protected  function AliyunBankcard($className){
		importTTF("AliyunBankcard.class.php");
		if(class_exists($className)){
			$class = new $className();
			return $class;
		}
		return false;
	}
	/**
	 * 明达伟亮四要素验证
	 * @date: 2017年3月6日 下午2:17:35
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function VerifyBankCardInfo($params){
		if(parent::checkParams($params,array(
			"bid", 
			"pid", 
			"accountNo", 
			"bankPreMobile", 
			"idCard"
		))){
			$pid = $params['pid'];
			$bid = $params['bid'];
			$rre = M("business")->field("realName")->where("id=" . $bid)->find();
			$data = array(
				"name" => $rre['realName'],  //持卡人
				"idCard" => $params['idCard'],  //身份证号
				"bank_card" => $params['accountNo'],
				"platform_id" => $pid
			);
			$class = $this->AliyunBankcard("BankCard");
			$class->setParams("realName",$rre['realName']);
			$class->setParams("Mobile",$params['bankPreMobile']);
			$class->setParams("cardNo",$params['idCard']);
			$class->setParams("bankcard",$params['accountNo']);
			$result = $class->handle();
			$data['reason'] = $result['msg'];
			$data['phone'] = $params['bankPreMobile'];
			self::addFourAuth($data,$result['status']);
			if($result['status'] == 1){
				$status = array(
						"status" => 1,
						"msg" => "认证成功"
				);
			}else{
				$status = array(
						"status" => 0,
						"msg" => $result['msg']
				);
			}
			/*
			importTTF("ThreeElementAuth.class.php");
			$rre = M("business")->field("realName")->where("id=" . $bid)->find();
			$data = array(
				"name" => $rre['realName'],  //持卡人
				"idCard" => $params['idCard'],  //身份证号
				"bank_card" => $params['accountNo'], 
				"platform_id" => $pid
			); //银行卡号
			$verifiData = array(
				"name" => $rre['realName'],  //持卡人
				"idCard" => $params['idCard'],  //身份证号
				"bank_card" => $params['accountNo']
			);
			$row = fRec("four_auth",$data,"status,reason");
			if($row){
				if($row['status'] == 1){
					$status = array(
						"status" => 1, 
						"msg" => "认证成功"
					);
				}else{
					$obj = new \ThreeElementAuth();
					$result = $obj->check($verifiData);
					$data['reason'] = $result['msg'];
					$data['phone'] = $params['bankPreMobile'];
					$this->addFourAuth($data,$result['status']);
					if($result['status'] == 1){
						$status = array(
							"status" => 1, 
							"msg" => "认证成功"
						);
					}else{
						$status = array(
							"status" => 0, 
							"msg" => $result['msg']
						);
					}
				}
			}else{
				$obj = new \ThreeElementAuth();
				$result = $obj->check($verifiData);
				$data['reason'] = $result['msg'];
				$data['phone'] = $params['bankPreMobile'];
				$this->addFourAuth($data,$result['status']);
				if($result['status'] == 1){
					$status = array(
						"status" => 1, 
						"msg" => "认证成功"
					);
				}else{
					$status = array(
						"status" => 0, 
						"msg" => $result['msg']
					);
				}
			}
			*/
		}else{
			$status = array(
				"status" => 0, 
				"msg" => "缺少信息"
			);
		}
		return ($status);
	}
	/**
	 * 四要素 验证 添加
	 * @date: 2017年3月6日 下午2:31:15
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:
	 * @return:
	 */
	protected function addFourAuth($data, $status){
		if(parent::checkParams($data,array(
			"name", 
			"idCard", 
			"bank_card", 
			"phone", 
			"reason", 
			"platform_id"
		))){
			switch($status){
				case 1:
					$status = 1;
					break;
				case 2:
					$status = 2;
					break;
				default:
					return false;
					break;
			}
			$data = array(
				"name" => $data['name'], 
				"idCard" => $data['idCard'], 
				"bank_card" => $data['bank_card'], 
				"phone" => $data['phone'], 
				"status" => $status, 
				"reason" => $data['reason'], 
				"createTime" => time(), 
				"platform_id" => $data['platform_id']
			);
			if(aRec("four_auth",$data)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	/**
	 * curl get post
	 */
	private static function curl_re($url, $data = null, $second = 30){
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
	 * 区分 储蓄卡  信用卡
	 * @date: 2017年6月7日 下午3:10:35
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function checkCards(){
		$flag = true;
		$params = I("post.");
		if(parent::checkParams($params,array(
			"cardNum"
		))){
			$url = "https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=" . $params['cardNum'] . "&cardBinCheck=true";
			$response = json_decode(curlRequest($url),true);
			if($response){
				if($response['cardType'] == 'CC'){
					$flag = false;
					$message = "不能填写信用卡";
				}else{
					$bankName = parent::getBankName($response['bank']);
					//$this->getCardType($response['cardType']);
				}
			}else{
				$flag = false;
				$message = "无效卡号";
			}
		}else{
			$flag = false;
			$message = "输入卡号";
		}
		if($flag){
			$status = array(
				"status" => 1, 
				"bankName" => $bankName
			);
		}else{
			$status = array(
				"status" => 0, 
				'msg' => $message
			);
		}
		echo json_encode($status);
	}
	/**
	 * 根据代码获取卡类型
	 */
	protected function getCardTypes($type){
		switch($type){
			case "DC":
				return "储蓄卡";
				break;
			case "CC":
				return "信用卡";
				break;
			default:
				return "未知";
				break;
		}
	}
}

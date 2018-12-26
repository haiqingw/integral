<?php
/**
 * +-------------------------------------------
 * | Description: 银行卡四要素验证
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年3月12日 上午11:05:53
 * +-----------------------------------------------------
 * | Filename: Bankcard.class.php
 * +-----------------------------------------------------
 */
namespace Common\Api;
header("Content-Type: text/html; charset=UTF-8");
define("PUB_TTF","./Public/ttf/class/");
class Bankcard{
	function importTTFC($className){
		return require_cache(PUB_TTF . $className);
	}
	/**
	 * 调用基础类公共方法
	 * @param string $className
	 */
	protected function AliyunBankcard($className){
		$this->importTTFC("AliyunBankcard.class.php");
		if(class_exists($className)){
			$class = new $className();
			return $class;
		}
		return false;
	}
	/**
	 * 四要素验证
	 * @date: 2017年11月13日 下午4:02:45
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function bank_card_four_elements($bid, $bankCard, $phone, $name, $idCard, $plat){
		//参数验证
		if(!empty($bid) || !empty($cardNum) || !empty($phone)){
			//获取实名信息 （姓名 、 身份证号）
			$data = array(
				"name" => $name,  //持卡人
				"idCard" => $idCard,  //身份证号
				"bank_card" => $bankCard, 
				"plat" => $plat
			); //银行卡号
			$class = $this->AliyunBankcard("BankCard");
			$class->setParams("realName",$name);
			$class->setParams("Mobile",$phone);
			$class->setParams("cardNo",$idCard);
			$class->setParams("bankcard",$bankCard);
			$result = $class->handle();
			$data['reason'] = $result['msg'];
			$data['phone'] = $phone;
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
		}else{
			$status = array(
				"status" => 0, 
				"msg" => "添加信息有误"
			);
		}
		return $status;
	}
	/**
	 * 四要素 验证 添加
	 * @date: 2017年3月6日 下午2:31:15
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:
	 * @return:
	 */
	protected static function addFourAuth($data, $status){
		if(checkParams($data,array(
			"name", 
			"idCard", 
			"bank_card", 
			"phone", 
			"reason", 
			"plat"
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
				"plat" => $data['plat']
			);
			aRec("four_auth",$data);
		}else{
			return false;
		}
	}
}
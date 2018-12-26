<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2017年12月20日 上午10:47:15
# Filename: OperatingController.class.php
# Description: 应用操作类
#================================================
namespace App\Controller;

use Think\Controller;

class OperatingController extends BaseController
{
	/**
	 * 实名认证
	 * @date: 2017年11月10日 上午10:23:19
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function realnameauth($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$idCard = trim($params['idcard']);
			$name = trim($params['name']);
			$idCardVerify = parent::check_id($idCard);
			if (!$idCardVerify) {
				$ret["responseStatus"] = 4001;
			} else {
				if (self::checkName($name)) {
					$checkAudit = M(T_BAMA)->where(array(
						"bid" => $bid
					))->getField("status");
					if ($checkAudit == 1) {
						$ret["responseStatus"] = 4002;
					} else {
						if (self::upDateCert($bid, $name, $idCard)) {
							$ret["responseStatus"] = 1;
						} else {
							$ret["responseStatus"] = 2002;
						}
					}
				} else {
					$ret["responseStatus"] = 4003;
				}
			}
		}
		return $ret;
	}
	/**
	 * 实名认证  数据更新
	 * @date: 2017年11月13日 下午3:09:01
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $bid 商户ID  $name 商户姓名   $idCard 证件号码
	 * @return:
	 */
	protected static function upDateCert($bid, $name, $idCard)
	{
		$flag = true;
		if (!empty($bid) || !empty($name) || !empty($idCard)) {
			if (cRec(T_CERT, array("bid" => $bid))) {
				if (cRec(T_CERT, array(
					"bid" => $bid, "name" => $name,
					"cardNum" => $idCard
				))) {
					$flag = true;
				} else {
					$data = array(
						"name" => $name,
						"cardNum" => $idCard
					);
					if (!uRec(T_CERT, $data, array(
						"bid" => $bid
					))) {
						$flag = false;
					}
				}
			} else {
				$data = array(
					"bid" => $bid,
					"name" => $name,
					"cardNum" => $idCard,
					"status" => 1,
					"createTime" => time()
				);
				if (!aRec(T_CERT, $data)) {
					$flag = false;
				}
			}
		} else {
			$flag = false;
		}
		return $flag;
	}
	/**
	 * 删除收货地址
	 * @date: 2018年4月12日 上午11:28:40
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function delshipping($params)
	{
		$id = $params['id'];
		$data = array("status" => 2);
		if (uRec(T_SPP_ADDRE, $data, "id=" . $id)) {
			$ret['responseStatus'] = 1;
		} else {
			$ret["responseStatus"] = 302;
		}
		return $ret;
	}
	/**
	 * 修改收货地址
	 * @date: 2018年4月12日 上午9:46:12
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function editshippingaddress($params)
	{
		$id = $params['id'];
		$province = $params['province'];
		$city = $params['city'];
		$area = $params['area'];
		$data = array(
			"name" => $params['name'],
			"phone" => $params['consigeephone'],
			"province" => $province,
			"city" => $city, "area" => $area,
			"address" => $params['address']
		);
		if (uRec(T_SPP_ADDRE, $data, array(
			"id=" . $id
		))) {
			$ret['responseStatus'] = 1;
		} else {
			$ret['responseStatus'] = 407;
		}
		return $ret;
	}
	/**
	 * 收货地址默认状态修改
	 * @date: 2018年4月11日 下午4:36:08
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function addrdefultstatus($params)
	{
		$id = $params['id'];
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			if (uRec(T_SPP_ADDRE, "defaultState=1", "id=" . $id)) {
				$ret['responseStatus'] = 1;
				$where = array(
					"bid" => $bid,
					"id" => array("neq", $id)
				);
				if (cRec(T_SPP_ADDRE, $where)) {
					uRec(T_SPP_ADDRE, "defaultState=2", $where);
				}
			} else {
				$ret['responseStatus'] = 407;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 收货地址添加
	 * @date: 2018年4月11日 下午4:16:12
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function shippingaddr($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$province = $params['province'];
			$city = $params['city'];
			$area = $params['area'];
			$data = array(
				"bid" => $bid,
				"name" => $params['name'],
				"phone" => $params['consigeephone'],
				"province" => $province,
				"city" => $city, "area" => $area,
				"address" => $params['address'],
				"createTime" => time(),
				"status" => 1,
				"defaultState" => $params['defaultState'],
				"plat" => $pid
			);
			$id = aRec(T_SPP_ADDRE, $data);
			if ($id) {
				$ret['responseStatus'] = 1;
				$where = array(
					"bid" => $data['bid'],
					"id" => array("neq", $id)
				);
				if (cRec(T_SPP_ADDRE, $where)) {
					if ($params['defaultState'] == 1) {
						uRec(T_SPP_ADDRE, "defaultState=2", $where);
					}
				}
			} else {
				$ret['responseStatus'] = 406;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 忘记密码
	 * @param unknown $params
	 * @return number
	 */
	public function forget($params)
	{
		$phone = trim($params['phone']);
		$pass = trim($params['newpass']);
		$bus = M(T_BUS)->field("phone,pwd")->where("phone=" . $phone)->find();
		if ($bus) {
			if (md5($pass) == $bus['pwd']) {
				$ret['responseStatus'] = 402;
			} else {
				if (uRec(T_BUS, array(
					"pwd" => md5($pass)
				), array(
					"phone" => $phone,
					"status" => 1
				))) {
					$ret['responseStatus'] = 1;
				} else {
					$ret['responseStatus'] = 407;
				}
			}
		} else {
			$ret['responseStatus'] = 201;
		}
		return $ret;
	}
	/**
	 * 修改密码
	 * @param unknown $params
	 * @return number
	 */
	public function repass($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$oldpass = trim($params['oldpass']);
			$newpass = trim($params['newpass']);
			$where = array(
				"id" => $bid,
				"pwd" => md5($oldpass),
				"plat" => $pid
			);
			if (cRec(T_BUS, $where)) {
				if (uRec(T_BUS, "pwd=" . md5($newpass), $where)) {
					$ret['responseStatus'] = 1;
				} else {
					$ret['responseStatus'] = 402;
				}
			} else {
				$ret['responseStatus'] = 403;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 默认提现参数返回
	 * @date: 2018年5月26日 上午10:01:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @return: array
	 */
	protected static function default_draw_set_params()
	{
		return array(
			"pou" => DEFAULT_DRWA_POU,
			"minMoney" => DEFAULT_MIN_MONEY,
			"maxMoney" => DEFAULT_MAX_MONEY,
			"startTime" => DEFAULT_DRAW_START_TIME,
			"endTime" => DEFAULT_DRAW_END_TIME,
			"num" => DEFAULT_DRAW_NUM,
			"tax" => DEFAULT_DRWA_TAX
		);
	}
	/**
	 * 提现
	 * @param  array $params
	 * ----------------------
	 * 必传参数
	 * ----------------------
	 * userID
	 * userPhone
	 * platformID
	 * money -> 提现金额
	 * ----------------------
	 * 1.商户账号验证 -> 查看是否可提现
	 * 2.平台提现时间判断
	 * 3.单商户当天提现次数验证
	 * 4.提现金额与当前商户余额对比
	 * 5.每次最低-最高提现金额判断
	 * 6.银行卡绑定验证
	 * 7.修改余额
	 * 8.记录余额变更
	 * 9.完成
	 * {requestType: ’operating’,requestKeywords:’drawcash’,userID:x,userPhone:x,platformID:x,money:x}
	 * @return number
	 * -----------------------------
	 * getDrawcashStatus			获取账号提现状态
	 * drawStatus       		  	是否已设置提现参数
	 * drawSet  		 	 	  	获取商户提现设置
	 * default_draw_set_params 		默认提现参数返回
	 * account_bank_card  			获取银行卡信息
	 * busBalance     				获取商户余额
	 *  a
	 */
	public function drawcash($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			//是否可以提现

			if (false) {
				$ret['responseStatus'] = 500;
			} else {
				//检账户状par态是否正常
				if (!parent::getDrawcashStatus($bid)) {
					$ret['responseStatus'] = 501;
				} else {
					$check = true;
					$user_level = M(T_BUS)->where(array("id" => $bid))->getField("level");
					if (parent::drawStatus($pid, $user_level)) {
						$drawSet = parent::drawSet($pid, $user_level);
						if ($drawSet['drawStatus'] == 2 || $drawSet['drawStatus'] == 3) {
							$check = false;
						}
					} else {
						$drawSet = self::default_draw_set_params();
					}
					// dump($drawSet);
					// exit();
					if (!$check) {
						$ret['responseStatus'] = 500;
					} else {
						//平台设置商户提现
						//当日提现次数
						$drawcon = parent::drawCount($bid);
						if ($drawcon > $drawSet['num']) {
							$ret['responseStatus'] = 508;
						} else {
							$cashType = $params['cashType'];
							$poundage = $drawSet['pou'] ? $drawSet['pou'] : DEFAULT_DRWA_POU;
							//提现金额
							$money = parent::subDecimals($params['money']);
							//税点
							$tax = parent::subDecimals($money * $drawSet['tax']);
							//扣除手续费
							$arrivemoney = parent::subDecimals($money - $poundage);
							//扣除税
							$arrivemoney = parent::subDecimals($arrivemoney - $tax);
							//获取商户银行卡信息
							$bankinfo = parent::account_bank_card($bid);
							$ye = parent::busBalance($bid);
							//可提现余额
							$obj = A("Business");
							$ktx = $obj->canWithdraw($bid, $pid, $ye, $drawSet['setMethod'], $cashType);
							if ($money > $ktx) {
								$ret['responseStatus'] = 502;
							} else {
								//然后判断是否达到系统设置的提现金额
								if ($money < $drawSet['minMoney'] || $money > $drawSet['maxMoney']) {
									$ret['responseStatus'] = 503;
								} else {
									$now = date("H:i:s");
									//验证提现时间段
									if ($now >= $drawSet['startTime'] && $now <= $drawSet['endTime']) {
										$ordernum = BSYTX . parent::generate_order_number();
										//更改已提现总额
										$walletData = array(
											"bid" => $bid, 
											//类型   YE
											"money" => $money,
											"cashType" => $cashType,
											//变更余额  P 存储  T 提现
											"tranType" => 'T',
											"ordernum" => $ordernum, 
											//'存储类型  JY 交易 JH 激活 QD 签到 RW 任务 SD 手动 YJ 押金返还 TX 提现',
											"storageType" => "TX",
											"platform_id" => $pid
										);
										//资金变更接口
										$obj = new \Common\Api\SecStatistics();
										$a = $obj->storage($walletData);
										if ($a) {
											//不在提现时间提现 加入审核列表
											adLog($bid, '订单号:' . $ordernum . '在非提现时间内提现,已加入审核列表,提现金额:[' . $money . '元]', false);
											$drawRet = parent::updateWithdraw(array(
												"bid" => $bid, "cashType" => $cashType, "money" => $arrivemoney,
												"bankId" => $bankinfo['id'], "ordernum" => $ordernum, "remark" => "收益提现", "platform_id" => $pid
											));
											if ($drawRet) {
												$ob = new \Common\Api\Accountdynamic();
												$ob->storage($pid, $bid, $ordernum, $arrivemoney);
											}
											$ret['responseStatus'] = 1;
										} else {
											//余额扣除失败
											$ret['responseStatus'] = 504;
										}
									} else {
										$ret['responseStatus'] = 507;
									}
								}
							}
						}
					}
				}
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 检银行卡是否添加
	 * @date: 2018年4月16日 下午4:29:11
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function checkbankcard($params)
	{
		// 			{requestType: ’operating’,requestKeywords:’checkbankcard’, platformID:’x’, userID:’x’,userPhone:x}
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$check = M(T_BNK)->where(array(
				"bid" => $bid, "plat" => $pid
			))->count();
			if ($check) {
				$ret['responseStatus'] = 1;
			} else {
				$ret['responseStatus'] = 202;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 添加银行卡
	 * @return boolean
	 */
	public function addbankcard($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$openBank = empty($params['openBank']) ? "中国银行新华东街支行" : $params['openBank'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			// 			{requestType: ’operating’,requestKeywords:’addbankcard’, platformID:’x’, userID:’x’,userPhone:’x’cardNum:x,phone:x,bankName:x,openBank:x,}	
			$user = M(T_CERT)->field("name,cardNum")->where(array(
				"bid" => $bid, "status" => 1
			))->find();
			if (!$user) {
				$ret['responseStatus'] = 4005;
			} else {
				$data = array(
					"bid" => $bid,
					"name" => $user['name'],
					"bankName" => $params['bankName'],
					"card_number" => $params['cardNum'],
					"open_bank" => $openBank,
					"plat" => $pid,
					"phone" => $params['phone']
				);
				// 				$checkAudit = M(T_BAMA)->where(array(
				// 					"bid" => $bid))->getField("status");
				// 				if($checkAudit == 2 || $checkAudit == 3){
				$threeE = $this->verifyThreeElement($bid, $data["card_number"], trim($params['phone']), $user['name'], $user['cardNum'], $pid);
				if ($threeE['status']) {
					$updateResult = self::upDateBank($data);
					if ($updateResult) {
						$ret['responseStatus'] = 1;
						// 							uRec(T_BAMA,array(
						// 								"status" => 1),array(
						// 								"bid" => $bid));
					} else {
						$ret['responseStatus'] = 2002;
					}
				} else {
					$ret = array(
						"responseStatus" => 205,
						"msg" => $threeE['msg']
					);
				}
				//准备数据
				// 				}else{
				// 					$ret['responseStatus'] = 204;
				// 				}
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 更新银行卡信息
	 * @date: 2017年11月28日 下午4:17:22
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function upDateBank($params)
	{
		$flag = true;
		if (!checkParams($params, array(
			"bid",
			"card_number", "bankName", "name",
			"open_bank", "plat", "phone"
		))) {
			$flag = false;
		} else {
			$data = array(
				'bid' => $params['bid'],
				'card_number' => trim($params['card_number']),
				'city' => "呼和浩特",
				'opening_bank' => $params['open_bank'],
				'bank_name' => trim(preg_replace("/\d|\(|\)/", "", $params['bankName'])),
				'name' => $params['name'],
				'addtime' => time(),
				'plat' => $params['plat'],
				"phone" => $params['phone']
			);
			$where = array(
				"bid" => $data['bid'],
				"plat" => $data['plat']
			);
			$check = M(T_BNK)->where($where)->count();
			if ($check) {
				$upData = array(
					"card_number" => $data['card_number'],
					"bank_name" => $data['bank_name'],
					"name" => $data['name'],
					"opening_bank" => $data['opening_bank'],
					"phone" => $data['phone']
				);
				if (!uRec(T_BNK, $upData, $where)) {
					$flag = false;
				}
			} else {
				$id = aRec(T_BNK, $data);
				if (!$id) {
					$flag = false;
				}
			}
		}
		return $flag;
	}
	/**
	 * 调用基础类公共方法
	 * @param string $className
	 */
	protected function AliyunBankcard($className)
	{
		importTTFC("AliyunBankcard.class.php");
		if (class_exists($className)) {
			$class = new $className();
			return $class;
		}
		return false;
	}
	/**
	 * 三要素验证
	 * @date: 2017年11月13日 下午4:02:45
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected function verifyThreeElement($bid, $bankCard, $phone, $name, $idCard, $plat)
	{
		//参数验证
		if (!empty($bid) || !empty($cardNum) || !empty($phone)) {
			//验证引入
			//importTTFC("JDBankverify.class.php");
			//获取实名信息 （姓名 、 身份证号）
			$data = array(
				"name" => $name,  //持卡人
				"idCard" => $idCard, //身份证号
				"bank_card" => $bankCard, "plat" => $plat
			); //银行卡号
			$class = $this->AliyunBankcard("BankCard");
			$class->setParams("realName", $name);
			$class->setParams("Mobile", $phone);
			$class->setParams("cardNo", $idCard);
			$class->setParams("bankcard", $bankCard);
			$result = $class->handle();
			$data['reason'] = $result['msg'];
			$data['phone'] = $phone;
			self::addFourAuth($data, $result['status']);
			if ($result['status'] == 1) {
				$status = array(
					"status" => 1,
					"msg" => "认证成功"
				);
			} else {
				$status = array(
					"status" => 0,
					"msg" => $result['msg']
				);
			}
		} else {
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
	protected static function addFourAuth($data, $status)
	{
		if (checkParams($data, array(
			"name",
			"idCard", "bank_card", "phone",
			"reason", "plat"
		))) {
			switch ($status) {
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
			aRec("four_auth", $data);
			//M("four_auth")->add($data);
		} else {
			return false;
		}
	}
	/**
	 * 区分 储蓄卡  信用卡
	 * @date: 2017年6月7日 下午3:10:35
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function checkcards($params)
	{
		$cardNum = $params['cardNum'];
		$url = "https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=" . $cardNum . "&cardBinCheck=true";
		$response = json_decode(curlRequest($url), true);
		if ($response) {
			if ($response['cardType'] == 'CC') {
				$ret['responseStatus'] = 207;
			} else {
				$bankName = parent::getBankName($response['bank']);
				$ret = array(
					"responseStatus" => 1,
					"bankName" => $bankName
				);
				//$this->getCardType($response['cardType']);
			}
		} else {
			$ret['responseStatus'] = 203;
		}
		return $ret;
	}
	/**
	 * 根据代码获取卡类型
	 */
	protected function getCardTypes($type)
	{
		switch ($type) {
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
	public function feedback($params)
	{
	}
	/**
	 * 发送短信
	 * @param unknown $params
	 * @return number
	 * {requestType: ’operating’,requestKeywords:’sendmsg’,phone:x,type:'register',code:x}
	 */
	public function sendmsg($params)
	{
		$phone = trim($params['phone']);
		$pid = empty($params['platformID']) ? "" : $params['platformID'];
		$code = empty($params['code']) ? "" : $params['code'];
		if (empty($pid)) {
			$pid = parent::getValue(T_BUS, "phone", $phone, "plat");
		}
		switch ($params['type']) {
			case "register":
				if (cRec(T_BUS, array(
					"phone" => $phone,
					"status" => 1
				))) {
					$ret['responseStatus'] = 3004;
				} else {
					$info = M(T_BUS)->field("plat")->where("code=" . $code . " and status = 1")->find();
					if ($info) {
						$verify = rand(1000, 9999);
						$result = parent::phoneVerifyCode($phone, $verify, "register", $info['plat']);
						if ($result) {
							$ret = array(
								"responseStatus" => 1,
								"code" => $verify
							);
						} else {
							$ret['responseStatus'] = 400;
						}
					} else {
						$ret['responseStatus'] = 209;
					}
				}
				break;
			case "forget":
				$info = M(T_BUS)->field("id,status")->where("phone=" . $phone . " and status = 1 and plat=" . $pid)->find();
				if ($info) {
					$verify = rand(1000, 9999);
					$result = parent::phoneVerifyCode($phone, $verify, "register", $pid);
					if ($result) {
						$ret = array(
							"responseStatus" => 1,
							"code" => $verify
						);
					} else {
						$ret['responseStatus'] = 400;
					}
				} else {
					$ret['responseStatus'] = 201;
				}
				break;
			case "bankcard":
				$verify = rand(1000, 9999);
				$result = parent::phoneVerifyCode($phone, $verify, "register", $pid);
				if ($result) {
					$ret = array(
						"responseStatus" => 1,
						"code" => $verify
					);
				} else {
					$ret['responseStatus'] = 400;
				}
				break;
			default:
				$ret['responseStatus'] = 301;
				break;
		}
		return $ret;
	}
	/**
	 * 增加阅读次数
	 * @param unknown $params
	 * @return number
	 */
	public function addviewnum($params)
	{
		$id = $params['id'];
		switch ($params['type']) {
			case "sysmsg":
				M('message')->where('message_id=' . $id)->setInc('views');
				break;
			case "helpcenter":
				M('helpcenter')->where('e_h_id=' . $id)->setInc('views');
				break;
			case "tips":
				M('tips')->where('e_h_id=' . $id)->setInc('views');
				break;
			default:
				$ret['responseStatus'] = 301;
				return $ret;
				break;
		}
		$ret['responseStatus'] = 1;
		return $ret;
	}
}
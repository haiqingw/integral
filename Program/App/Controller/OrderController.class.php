<?php

/**
 * +-------------------------------------------
 * | Description: 商户订单管理 
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年4月12日 下午3:05:25
 * +-----------------------------------------------------
 * | Filename: OrderController.class.php
 * +-----------------------------------------------------
 */
namespace App\Controller;

use Think\Controller;
use Common\Api\ImageManage;

class OrderController extends BaseController
{
	/**
	 * 统计数
	 * @date: 2018年4月24日 上午11:45:04
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’Order’,requestKeywords:’getnum’,platformID:’x’,userID:’x’,userPhone:’x’}
	 */
	public function getnum($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$delivery = 0;
			$goods = 0;
			$eval = 0;
			$where = " and isOrder = 1 and wxIsPay = 2  and bid =  " . $bid;
			//待收货数
			$sql = "select COUNT(*)con from " . PREFIX . T_ORDER_TAB . " where isReceipt=2 " . $where;
			$query = M()->query($sql);
			if ($query) {
				$delivery = $query[0]['con'];
			}
			//待发货数
			$sql = "select COUNT(*)con from " . PREFIX . T_ORDER_TAB . " where isReceipt=3 " . $where;
			$query = M()->query($sql);
			if ($query) {
				$goods = $query[0]['con'];
			}
			//待评价数
			$sql = "select COUNT(*)con from " . PREFIX . T_ORDER_TAB . " where isReceipt=4 " . $where;
			$query = M()->query($sql);
			if ($query) {
				$eval = $query[0]['con'];
			}
			$ret = array(
				"responseStatus" => 1,
				"delivery" => $delivery,
				"goods" => $goods, "eval" => $eval
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 删除出订单
	 * @date: 2018年4月23日 上午11:29:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function deleteorder($params)
	{
		$id = $params['id'];
		if (uRec(T_ORDER_TAB, array("isOrder" => 3), array(
			"id" => $id
		))) {
			$ret["responseStatus"] = 1;
		} else {
			$ret['responseStatus'] = 2002;
		}
		return $ret;
	}
	/**
	 * 取消订单
	 * @date: 2018年4月23日 上午11:27:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’Order’,requestKeywords:’cancelorder’,id:x(订单ID)}
	 */
	public function cancelorder($params)
	{
		$id = $params['id'];
		$isRec = parent::getValue(T_ORDER_TAB, "id", $id, "isReceipt");
		if ($isRec == 2) {
			$ret['responseStatus'] = 208;
		} else {
			if (uRec(T_ORDER_TAB, array(
				"isOrder" => 2
			), array("id" => $id))) {
				$ret["responseStatus"] = 1;
			} else {
				$ret['responseStatus'] = 2002;
			}
		}
		return $ret;
	}
	/**
	 * 收货确认状态
	 * @date: 2018年4月23日 上午11:21:39
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’Order’,requestKeywords:’confirmstatus’,id:x(订单ID)}
	 */
	public function confirmstatus($params)
	{
		$id = $params['id'];
		if (uRec(T_ORDER_TAB, array(
			"isReceipt" => 4
		), array("id" => $id))) {
			//修改签收状态
			uRec(T_ORDER_TAB, array(
				"isShip" => 2,
				"shipTime" => time()
			), array(
				"id" => $id
			));
			$ret["responseStatus"] = 1;
		} else {
			$ret['responseStatus'] = 2002;
		}
		return $ret;
	}
	/**
	 * 评论
	 * @date: 2018年3月21日 下午4:24:49
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’Order’,requestKeywords:’comment’, platformID:’x’,userID:’x’,userPhone:’x’ ,score:x(评分),content:x(内容),orderid:x(订单ID)}
	 */
	public function comment($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$productID = parent::getValue(T_ORDER_TAB, "id", $params['orderid'], "proid");
			$data = array(
				"content" => $params['content'],
				"bid" => $bid,
				"productid" => $productID,
				"createTime" => time(),
				"status" => 2,
				"orderid" => $params['orderid'],
				"score" => $params['score'],
				"plat" => $pid
			);
			/**Todo
			 * 1.验证商户状态
			 * 2.检查订单是否已完成（未完成不能评论）
			 * 3.平台产品是否存在
			 */
			if (cRec(T_COMMENT_BUS, array(
				"orderid" => $params['orderid'],
				"bid" => $bid, "plat" => $pid
			))) {
				$ret['responseStatus'] = 409;
			} else {
				if (aRec("comment", $data)) {
					$ret['responseStatus'] = 1;
					//评论成功修改订单状态（已完成）
					uRec(T_ORDER_TAB, array(
						"isReceipt" => 1
					), array(
						"id" => $data['orderid']
					));
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
	 * 支付状态查询
	 * @date: 2018年4月17日 下午3:32:57
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @return int 1 已支付 2 未支付
	 */
	public function orderquery($params)
	{
		$where = array(
			"id" => $params['id'],
			"ordernum" => $params['ordernum']
		);
		if (cRec(T_ORDER_TAB, $where)) {
			$isPay = IS_STATUS_ONE;
			$wxPayStatus = M(T_ORDER_TAB)->where($where)->getField("wxIsPay");
			if ($wxPayStatus != IS_STATUS_TWO) {
				$isPay = IS_STATUS_TWO;
			}
			$ret = array(
				"responseStatus" => 1,
				"isPay" => $isPay
			);
		} else {
			$ret['responseStatus'] = 104;
		}
		return $ret;
	}
	/**
	 * 订单支付回调修改订单状态
	 * @date: 2018年4月13日 下午3:22:28
	 * @author: HaiQing.Wu <398994668@qq.com>d
	 */
	public function orderPay($params)
	{
		$params = array(
			"appid" => $params['appid'],
			"attach" => $params['attach'],
			"transaction_id" => $params['transaction_id'],
			"out_trade_no" => $params['out_trade_no'],
			"bank_type" => $params['bank_type'],
			"fee_type" => $params['fee_type'],
			"is_subscribe" => $params['is_subscribe'],
			"mch_id" => $params['mch_id'],
			"nonce_str" => $params['nonce_str'],
			"openid" => $params['openid'],
			"result_code" => $params['result_code'],
			"return_code" => $params['return_code'],
			"sign" => $params['sign'],
			"time_end" => $params['time_end'],
			"total_fee" => $params['total_fee'],
			"trade_type" => $params['trade_type']
		);
		$this->WxPayrecord($params);
		$where = array(
			"ordernum" => $params['out_trade_no'],
			"bid" => $params['attach']
		);
		if (cRec(T_ORDER_TAB, $where)) {
			$isDeposit = 1;
			$info = M(T_ORDER_TAB)->field("proid,depositMoney,plat,orderMoney")->where($where)->find();
			// $proid = M(T_ORDER_TAB)->where($where)->getField("proid");
			if (intval($info['depositMoney']) == 0) {
				$isDeposit = 2;
			}
			if (!empty(intval($info['orderMoney']))) {
				$obj = A("Business");
				$obj->updatePlatBalance($info['plat'], $info['orderMoney']);
			}
			$data = array(
				"isPay" => 1,
				"isDeposit" => 1, "wxIsPay" => 2,
				"payTime" => time(),
				"isReceipt" => 3,
				"isDepositRefund" => $isDeposit
			);
			uRec(T_ORDER_TAB, $data, $where);
			//更新商品库存
			self::alterCommondityInventoryAmount($info['proid']);
			//更新商品售量
			self::alterCommoditySoldAmount($info['proid']);
			//提示成功支付 订单发货
			self::pay_success_order_sms_notify($params['out_trade_no'], $params['attach']);
		}
	}
	/**
	 * 函数用途描述
	 * @date: 2018年6月13日 上午10:30:21
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function pay_success_order_sms_notify($ordernum, $bid)
	{
		$check = false;
		if (!empty($ordernum) && !empty($bid)) {
			$sql = "select getParentVipID(" . $bid . ") id";
			$query = M()->query($sql);
			if ($query[0]['id'] != 0) {
				$sql = "select plat id,phone from p_user where id = (" . $query[0]['id'] . ");";
				$query = M()->query($sql);
				if ($query) {
					$sendType = "serviceremind";
					$check = true;
				}
			} else {
				$sql = "select usertable_ID id,usertable_Phone phone from p_usertable where usertable_ID = (select plat from p_user where id = " . $bid . ") limit 1";
				$query = M()->query($sql);
				if ($query) {
					$sendType = "orderremind";
					$check = true;
				}
			}
			if ($check) {
				parent::phoneVerifyCode($query[0]['phone'], $ordernum, $sendType, $query[0]['id']);
			}
		}
	}
	/**
	 * 更新商品库存
	 * @date: 2018年4月13日 上午10:44:18
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function alterCommondityInventoryAmount($id)
	{
		if (!empty($id)) {
			$con = M(T_COMMODITY)->where("id=" . $id)->count();
			if ($con > 1) {
				$res = M(T_COMMODITY)->where("id=" . $id)->setDec("stock");
				if ($res) {
					return true;
				}
			}
		}
		return false;
	}
	/**
	 * 更新商品售量
	 * @date: 2018年4月13日 上午10:39:55
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $id 商品ID
	 * @return: boolean
	 */
	protected static function alterCommoditySoldAmount($id)
	{
		if (!empty($id)) {
			$res = M(T_COMMODITY)->where("id=" . $id)->setInc("sold");
			if ($res) {
				return true;
			}
		}
		return false;
	}
	/**
	 * 微信支付记录
	 * @date: 2018年4月17日 下午3:10:34
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected function WxPayrecord($data)
	{
		aRec("wxpaycord", $data);
	}
	/**
	 * 添加订单
	 * @date: 2018年4月12日 下午3:29:49
	 * @author: HaiQing.Wu <398994668@qq.com>
	 *  APP必传参数
	 * ------------------
	 * userID 商户id
	 * userPhone 商户登录 账号
	 * platformID 平台id
	 * proid 产品id
	 * pcid 产品分类id
	 * sid 收货地址id
	 * ordermoney订单金额
	 * depositmoney押金
	 * ------------------
	 */
	public function order($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			//重复订单号验证
			$ordernum = parent::generate_order_number();
			$money = empty($params['orderMoney']) ? DEFAULT_ORDER_MONEY : $params['orderMoney']; //订单金额
			$deposit = empty($params['deposit']) ? DEFAULT_DEPOSIT_MONEY : $params['deposit']; //押金
			$sum = $money + $deposit;
			$remark = empty($params['remark']) ? "" : $params['remark'];
			if (!cRec(T_ORDER_TAB, array(
				"ordernum" => $ordernum
			))) {
				$data = array(
					"bid" => $bid,
					"ordernum" => $ordernum,
					"orderTime" => time(),
					"plat" => $pid,
					"sid" => $params['sid'],
					"proid" => $params['proid'],
					"orderMoney" => $money,
					"depositMoney" => $deposit,
					"isOrder" => 1, "isPay" => 2,
					"isShip" => 1,
					"isDeposit" => 2,
					"isDepositRefund" => 4,
					"orderTime" => time(),
					"remark" => $remark,
					"isActivate" => 1
				);
				//产品分类ID
				$prcid = $this->procid($data['proid']);
				$data['procid'] = $prcid;
				//查看库存
				$inventory = parent::getValue(T_COMMODITY, "id", $data['proid'], "stock");
				if ($inventory == 0) {
					$ret['responseStatus'] = 601;
				} else {
					//二次验证收货地址
					$shipp = parent::getShipp($data['sid']);
					if (!$shipp) {
						$ret['responseStatus'] = 602;
					} else {
						$data['consignee'] = $shipp['name'];
						$data['consigneePhone'] = $shipp['phone'];
						$data['province'] = $shipp['province'];
						$data['city'] = $shipp['city'];
						$data['area'] = $shipp['area'];
						$data['address'] = $shipp['address'];
						$data['wxpayMoney'] = $sum;
						$data['wxIsPay'] = 1;
						$id = aRec(T_ORDER_TAB, $data);
						if ($id) {
							if (!empty(intval($sum))) {
								$dao = A("Wx");
								if ($params['types'] == 'sp') {
									$response = $dao->createunifiedordersp($ordernum, $sum, $params['openid'], $bid);
								} else {
									$response = $dao->createunifiedorder($ordernum, $sum, $params['openid'], $bid);
								}
								if ($response['return_code'] == 'SUCCESS') {
									$ret = array(
										"responseStatus" => 1,
										"prepay_id" => $response['prepay_id'],
										"id" => $id,
										"payType" => 1,
										"ordernum" => $data['ordernum']
									);
								} else {
									$ret = array(
										"responseStatus" => 103,
										"msg" => $response['return_msg']
									);
								}
							} else {
								$ret = array(
									"responseStatus" => 1,
									"id" => $id,
									"payType" => 2,
									"ordernum" => $data['ordernum']
								);
								$where = array(
									"ordernum" => $data['ordernum'],
									"bid" => $bid
								);
								if (cRec(T_ORDER_TAB, $where)) {
									$proid = M(T_ORDER_TAB)->where($where)->getField("proid");
									$data = array(
										"isPay" => 1,
										"isDeposit" => 1,
										"wxIsPay" => 2,
										"payTime" => time(),
										"isReceipt" => 3,
										"isDepositRefund" => 2
									);
									uRec(T_ORDER_TAB, $data, $where);
									//更新商品库存
									self::alterCommondityInventoryAmount($proid);
									//更新商品售量
									self::alterCommoditySoldAmount($proid);
									//提示成功支付 订单发货
									self::pay_success_order_sms_notify($data['ordernum'], $bid);
								}
							}
						} else {
							$ret['responseStatus'] = 2002;
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
	 * 获取商品分类ID
	 * @date: 2018年4月13日 上午11:21:25
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected function procid($pid)
	{
		if (!empty($pid)) {
			$cid = parent::getValue(T_COMMODITY, "id", $pid, "category_id");
			if ($cid) {
				return $cid;
			}
		}
		return false;
	}
	/**
	 * 获取物流信息
	 * @date: 2018年4月23日 下午6:37:47
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function CourierQuery($waybillNumber, $id)
	{
		$obj = new CourierqueryController();
		$courierInfo = $obj->getorder($waybillNumber, $id);
		return $courierInfo;
	}
	/**
	 * 物流跟踪
	 * @date: 2018年4月24日 上午9:39:28
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * --------------------------------------
	 * 调用方法 
	 * 1.CourierQuery   获取物流信息
	 */
	public function waybill($params)
	{
		$id = $params['id'];
		$field = array(
			"isOrder", "isReceipt",
			"wxIsPay", "waybillNumber",
			"courierName"
		);
		$info = M(T_ORDER_TAB)->field($field)->where(array(
			"id" => $id
		))->find();
		if ($info) {
			$data = array();
			$re = $this->CourierQuery($info['waybillNumber'], $id);
			foreach ($re['data'] as $key => $v) {
				$data[$key]['context'] = $v['context'];
				$data[$key]['ftime'] = $v['ftime'];
			}
			$ret = array(
				"responseStatus" => 1,
				"waybillNumber" => $info['waybillNumber'],
				"courierName" => $info['courierName'],
				"data" => $data
			);
		} else {
			$ret['responseStatus'] = 302;
		}
		return $ret;
	}
	/**
	 * 详情
	 * @date: 2018年4月16日 上午11:34:43
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function detail($params)
	{
		$id = $params['id'];
		$field = array(
			"id", "ordernum", "proid",
			"consignee", "consigneePhone",
			"concat(province,city,area,address) address",
			"orderMoney", "freight",
			"depositMoney", "wxpayMoney",
			"wxIsPay", "isReceipt", "isActivate",
			"isDepositRefund",
			"from_unixtime(orderTime,'%Y-%m-%d %H:%i:%s') orderTime",
			"from_unixtime(receiptTime,'%Y-%m-%d %H:%i:%s') receiptTime",
			"(select commodityName from " . PREFIX . T_COMMODITY . " c  where c.id = o.proid) productname",
			"(select rate from " . PREFIX . T_COMMODITY . " c  where c.id = o.proid) rate",
			"if(wxIsPay = 1,'" . NOT_PAY . "',case isReceipt when 1 then '" . ORDER_COMPLETED . "' when 2 then '" . BEEN_SHIP . "' when 3 then '" . TO_SEND . "' when 4 then '" . TO_EVALUATE . "' end) rstatus",
			"case isActivate when 1 then '" . NOT_ACTIVATED . "' when 2 then '" . BEEN_ACTIVATED . "' end jhzt",
			"case wxIsPay when 1 then '" . NOT_PAY . "' when 2 then '" . HAVE_PAY . "' end pay",
			"case isDepositRefund when 1 then '" . NOT_DEPOSIT . "' when 2 then '" . BEEN_DEPOSIT . "' when 3 then '" . REFUND_FAILURE . "' when 4 then '" . NOT_PAY . "' end  returned",
			"terminalNumber", "courierName",
			"waybillNumber"
		);
		$dao = M(T_ORDER_TAB . " o");
		$info = $dao->field($field)->where("id=" . $id)->find();
		if ($info) {
			if ($info['waybillNumber']) {
				$obj = A("Courierquery");
				$courierInfo = $obj->getorder($info['waybillNumber'], $id);
				if ($courierInfo) {
					$info['waybillContext'] = $courierInfo['data'][0]['context'];
					$info['waybillTime'] = $courierInfo['data'][0]['time'];
				} else {
					$info['waybillContext'] = "暂无物流信息";
					$info['waybillTime'] = $info['receiptTime'];
				}
			}
			$info['imgPath'] = $this->imagePath(self::get_imagepath_id($info['proid']));
			$ret = array(
				"responseStatus" => 1,
				"data" => $info
			);
		} else {
			$ret['responseStatus'] = 302;
		}
		return $ret;
	}
	/**
	 * 列表
	 * @date: 2018年4月16日 上午9:47:00
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function olist($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$page = $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$dates = empty($params['dates']) ? DEFAULT_SEARCH_DATE : $params['dates'];
			$field = array(
				"id", "ordernum",
				"proid", "orderMoney", "freight",
				"depositMoney", "wxpayMoney",
				"wxIsPay", "isReceipt", "isOrder",
				"isActivate", "isDepositRefund",
				"from_unixtime(orderTime,'%Y-%m-%d %H:%i:%s') orderTime",
				"(select commodityName from " . PREFIX . T_COMMODITY . " c  where c.id = o.proid) productname",
				"(select rate from " . PREFIX . T_COMMODITY . " c  where c.id = o.proid) rate",
				"if(wxIsPay = 1,'" . NOT_PAY . "',if(wxIsPay = 4 ,'" . HAVE_REFUND . "',case isReceipt when 1 then '" . ORDER_COMPLETED . "' when 2 then '" . BEEN_SHIP . "' when 3 then '" . TO_SEND . "' when 4 then '" . TO_EVALUATE . "' end)) rstatus",
				"case isActivate when 1 then '" . NOT_ACTIVATED . "' when 2 then '" . BEEN_ACTIVATED . "' end jhzt",
				"case wxIsPay when 1 then '" . NOT_PAY . "' when 2 then '" . HAVE_PAY . "' when 4 then '" . HAVE_REFUND . "' end pay",
				"case isDepositRefund when 1 then '" . NOT_DEPOSIT . "' when 2 then '" . BEEN_DEPOSIT . "' when 3 then '" . REFUND_FAILURE . "' when 4 then '" . NOT_PAY . "' end  returned"
			);
			$offset = ($page - 1) * $limit;
			$where = array(
				"bid" => $bid,
				"plat" => $pid,
				"isOrder" => array("neq", 3)
			);
			if ($params['isReceipt'] != 'All') {
				$where['isReceipt'] = $params['isReceipt'];
			}
			$dao = M(T_ORDER_TAB . " o");
			$array = $dao->field($field)->where($where)->limit($offset, $limit)->order("orderTime DESC")->select();
			$data = array();
			if ($array) {
				foreach ($array as $key => $val) {
					$data[$key]['id'] = $val['id'];
					$data[$key]['ordernum'] = $val['ordernum'];
					$data[$key]["yjje"] = $val['depositMoney']; //押金金额
					$data[$key]['productname'] = $val['productname']; //产品名称
					$data[$key]['ddje'] = $val['orderMoney']; //商品金额
					$data[$key]['zje'] = $val['wxpayMoney']; //订单总金额
					$data[$key]['yunfei'] = $val['freight']; //运费
					$data[$key]['isOrder'] = $val['isOrder'];
					$data[$key]['rate'] = $val['rate']; //费率
					$data[$key]['orderTime'] = $val['orderTime']; //订单时间
					$data[$key]['isPay'] = $val['wxIsPay']; // 支付状态  1 未付款 2 已付款
					$data[$key]['pay'] = $val['pay']; //支付状态提示  
					$data[$key]['activate'] = $val['jhzt']; //激活状态提示
					$data[$key]['isActivate'] = $val['isActivate']; //激活状态    1 未激活 2 已激活
					$data[$key]['isReceipt'] = $val['isReceipt']; //发货状态提示   isPay  返回 1   receipt 提示未付款
					$data[$key]['receipt'] = $val['rstatus']; //发货状态 1 订单完成  2 已发货 3 等待发货  4 待评价
					$data[$key]['isReturn'] = $val['isDepositRefund'];
					$data[$key]['returned'] = $val['returned'];
					$data[$key]['imgPath'] = $this->imagePath(self::get_imagepath_id($val['proid']));
				}
				$totalCount = $dao->where($where)->count();
				$ret = array(
					"responseStatus" => 1,
					"data" => $data,
					"count" => $totalCount
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		// 				dump($ret);
		return $ret;
	}
	/**
	 * 获取图片路径
	 * @date: 2018年3月17日 下午1:34:22
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:  $imgID 获取图片ID
	 * @return: array
	 */
	public function imagePath($imgID)
	{
		$obj = new ImageManage();
		$res = $obj->getImagePathArray($imgID);
		return $res;
	}
	protected static function get_imagepath_id($proid)
	{
		return parent::getValue(T_COMMODITY, "id", $proid, "imgPath");
	}
}
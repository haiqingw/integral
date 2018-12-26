<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2018年5月16日 下午5:23:13
# Filename: AgentController.class.php
# Description: 代理商类
#================================================
namespace App\Controller;

use Think\Controller;

class AgentController extends BaseController
{
	/**
	 * 退码
	 *
	 * @param [type] $params
	 * @return void
	 * {requestType: 'agent',requestKeywords:'backyards', platformID:x,userID:'x',userPhone:'x',childID:'x'（下级商户ID）,machineID:'x' 终端列表ID（接口输出的ID）,terminal:'x' 终端号} 
	 */
	public function backyards($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$terminal = $params['terminal'];
			$childID = $params['childID'];
			$machineID = $params['machineID'];
			$bid = parent::busID(
				array("phone" => $phone, "plat" => $pid)
			);
			// dump($params);
			// exit();
			if (cRec(
				"terminal_manage",
				array("bid" => $childID, "terminal" => $terminal, "plat" => $pid, "isActive" => 2)
			)) {
				$ret['responseStatus'] = 701;
			} else {
				//修改机具使用状态
				$updateUse = uRec(
					"machine_list",
					array("useStatus" => 1, "useTime" => "", "useID" => ""),
					array("belongID" => $bid, "terminalNo" => $terminal, "plat" => $pid)
				);
				if ($updateUse) {
					//终端查询管理
					dRec("terminal_manage", array("bid" => $childID, "terminal" => $terminal, "plat" => $pid));
					//清除装机记录
					dRec("machine_recods", array("pid" => $bid, "bid" => $childID, "terminal" => $terminal, "plat" => $pid));
					$ret['responseStatus'] = 1;
				} else {
					$ret['responseStatus'] = 2001;
				}
			}
			return $ret;
		}
	}
	/**
	 * 记录
	 * @return void
	 * {requestType: ’agent’,requestKeywords:'macrecords, platformID:x,userID:’x’,userPhone:’x’,proid:x（扩展参数 产品ID ）选填, page:x,limit:x} 
	 */
	public function macrecords($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(
				array("phone" => $phone, "plat" => $pid)
			);
			$page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$where = array(
				"pid" => $bid
			);
			empty($params['proid']) ? "" : $where['proid'] = $params['proid'];
			$field = array(
				"terminal",
				"(select commodityName from " . PREFIX . T_COMMODITY . " c  where c.id = proid) productname",
				"(select busname from " . PREFIX . T_BUS . " u where u.id = pid ) diabus",
				"(select busname from " . PREFIX . T_BUS . " u where u.id = bid ) usebus",
				"from_unixtime(createTime,'%Y.%m.%d %H:%i:%s') createTime",
			);
			$offset = ($page - 1) * $limit;
			$array = M('machine_recods')->field($field)->where($where)->limit($offset, $limit)->select();
			if ($array) {
				$totalCount = M('machine_recods')->where($where)->count();
				$ret = array(
					"responseStatus" => 1, "data" => $array, "count" => $totalCount
				);
			} else {
				$ret = array("responseStatus" => 300);
			}
		}
		return $ret;
	}
	/**
	 * 查看机具是否存在
	 * @date: 2018年7月18日 上午9:39:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:'checkterminal', platformID:x,userID:’x’,userPhone:’x’,keywords:x（终端号）} 
	 */
	public function checkterminal($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			if (cRec(T_MACL, array("terminalNo" => $params['keywords']))) {
				$info = M(T_MACL)->field("*,(select commodityName from p_commodity where id = productID) product")->where(array("terminalNo" => $params['keywords']))->find();
				if ($info['useStatus'] == 2) {
					$ret['responseStatus'] = 306;
				} else {
					$ret = array(
						"responseStatus" => 1,
						"id" => $info['id'],
						"product" => $info['product'],
						"productID" => $info['productID']
					);
				}
			} else {
				$ret['responseStatus'] = 307;
			}
		}
		return $ret;
	}
	/**
	 * 下级商户列表
	 * @date: 2018年7月18日 上午9:39:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:'getbuslist', platformID:x,userID:’x’,userPhone:’x’,keywords:x（索引=>商户名称，手机号）} 
	 */
	public function getbuslist($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$where['busname|phone'] = array(
				"like",
				"%" . $params['keywords'] . "%"
			);
			$where['parent'] = $bid;
			$where['status'] = 1;
			//$where['level'] = 1;
			$child_info = M(T_BUS)->field("id,busname,phone")->where($where)->select();
			if ($child_info) {
				$ret = array("responseStatus" => 1, "data" => $child_info);
				// $sql = "select getParentVipID(" . $child_info['id'] . ") pid";
				// $query = M()->query($sql);
				// if ($query) {
				// 	if ($bid == $query[0]['pid']) {
				// 		$ret = array("responseStatus" => 1, "data" => $child_info);
				// 	} else {
				// 		$ret = array("responseStatus" => 1005);
				// 	}
				// } else {
				// 	$ret = array("responseStatus" => 1005);
				// }
			} else {
				$ret = array("responseStatus" => 1006);
			}
		} else {
			$ret = array("responseStatus" => 1005);
		}
		return $ret;
	}
	/**
	 * 服务商一键式生成订单
	 * @date: 2018年7月18日 上午9:39:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:'getbus’, platformID:x,userID:’x’,userPhone:’x’,keywords:x（索引=>商户名称，手机号）} 
	 */
	public function getbus($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$where['busname|phone'] = array(
				"like",
				"%" . $params['keywords'] . "%"
			);
			$child_info = M(T_BUS)->field("id,busname,phone")->where($where)->find();
			if ($child_info) {
				$sql = "select getParentVipID(" . $child_info['id'] . ") pid";
				$query = M()->query($sql);
				if ($query) {
					if ($bid == $query[0]['pid']) {
						$ret = array("responseStatus" => 1, "data" => $child_info);
					} else {
						$ret = array("responseStatus" => 1005);
					}
				} else {
					$ret = array("responseStatus" => 1005);
				}
			} else {
				$ret = array("responseStatus" => 1005);
			}
		}
		return $ret;
	}
	/**
	 * 服务商一键式生成订单
	 * @date: 2018年7月18日 上午9:39:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’oneclick’, platformID:x,userID:’x’,userPhone:’x’,childID:x（下级商户ID）,machineID:x 终端列表ID（接口输出的ID）,terminal:x 终端号} //
	 */
	public function oneclick($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			//终端信息
			$machine_info = M(T_MACL)->field("terminalNo,productID,useStatus")->where(array(
				"id" => $params['machineID']
			))->find();
			if ($machine_info) {
				$data = array(
					"orderid" => 0,
					"isActive" => 1,
					"proid" => $machine_info['productID'],
					"bid" => $params['childID'],
					"terminal" => $params['terminal'],
					"plat" => $pid
				);
				//查看是否有信息
				if ($machine_info['useStatus'] == 2) {
					$ret['responseStatus'] = 306;
				} else {
					//终端比对是否相同
					if ($machine_info['terminalNo'] != $data['terminal']) {
						$ret['responseStatus'] = 307;
					} else {
						//查看是否存
						if (!cRec("terminal_manage", array("proid" => $data['proid'], "terminal" => $data['terminal']))) {
							//存储信息
							if (aRec("terminal_manage", $data)) {
								//更新终端使用状态 
								$mdata = array(
									"useID" => $params['childID'],
									"orderID" => 0,
									"useStatus" => 2,
									"useTime" => time()
								);
								uRec(T_MACL, $mdata, array(
									"terminalNo" => $machine_info['terminalNo'],
									"productID" => $machine_info['productID']
								));
								//更新商品库存
								// self::alterCommondityInventoryAmount($machine_info['productID']);
								//更新商品售量
								// self::alterCommoditySoldAmount($machine_info['productID']);
								$ret['responseStatus'] = 1;
								$this->machine_records($pid, $bid, $params['childID'], $machine_info['productID'], $machine_info['terminalNo']);
							} else {
								$ret['responseStatus'] = 2002;
							}
						} else {
							$ret['responseStatus'] = 306;
						}
					}
				}
			} else {
				$ret['responseStatus'] = 307;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 装机记录
	 * @date: 2018年7月26日 下午2:05:08
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected function machine_records($plat, $pid, $bid, $proid, $terminal)
	{
		if (!empty($plat) || !empty($pid) || !empty($bid) || !empty($proid)) {
			$data = array(
				"plat" => $plat,
				"pid" => $pid,
				"bid" => $bid,
				"proid" => $proid,
				"terminal" => $terminal,
				"createTime" => time()
			);
			aRec("machine_recods", $data);
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
	 * 服务商拨码
	 * @date: 2018年6月11日 下午2:43:17
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’dialcode’, platformID:x,userID:’x’,userPhone:’x’,childID:x,machineID:x(字符串 逗号隔开t如 ：1,2,3)} 拨码
	 */
	public function dialcode($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			$ex = explode(",", $params['machineID']);
			for ($i = 0; $i < count($ex); $i++) {
				$where = array("id" => $ex[$i]);
				$info = M(T_MACL)->where($where)->find();
				if ($info) {
					$data = array(
						"allot" => 2,
						"allotTime" => time(),
						"belongID" => $params['childID'],
						"codeManID" => $bid
					);
					if (uRec(T_MACL, $data, array("id" => $ex[$i]))) {
						$ret['responseStatus'] = 1;
						$this->addrecode($bid, $info['productID'], $ex[$i], $params['childID'], $pid);
						//修改记录表状态
						$sdata = array("status" => 2);
						uRec(T_MACDC, $sdata, array("productID" => $info['productID'], "machineID" => $ex[$i], "belongID" => $bid, "plat" => $pid));
					} else {
						$ret['responseStatus'] = 2002;
						break;
					}
				} else {
					$ret['responseStatus'] = 307;
					break;
				}
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 调拨记录
	 * @date: 2018年6月12日 上午9:20:42
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:$bid
	 * @param:$productID
	 * @param:$machineID
	 * @param:$belongID
	 * @param:$pid 
	 * @return:
	 */
	protected function addrecode($bid, $productID, $machineID, $belongID, $pid)
	{
		$data = array(
			"parentID" => $bid,
			"productID" => $productID,
			"machineID" => $machineID,
			"allotTime" => time(), "allot" => 2,
			"belongID" => $belongID,
			"plat" => $pid
		);
		if (!cRec(T_MACDC, array(
			"machineID" => $machineID,
			"productID" => $productID,
			"belongID" => $belongID
		))) {
			aRec(T_MACDC, $data);
		}
	}
	/**
	 * 服务商列表
	 * @date: 2018年6月11日 下午2:02:38
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’childlist’, platformID:x,userID:’x’,userPhone:’x’} 服务商列表
	 */
	public function childlist($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			$array = M(T_BUS)->field("id,busname,phone")->where(array(
				"parent" => $bid, "level" => 2
			))->select();
			if (!$array) {
				$ret['responseStatus'] = 300;
			} else {
				$ret = array("responseStatus" => 1, "data" => $array);
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 批次列表
	 * @date: 2018年6月4日 下午5:34:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’batchnolist’, platformID:x,userID:’x’,userPhone:’x’} 批次列表
	 */
	public function batchnolist($params)
	{
		// 		$params = parent::testParams();
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$sql = "select DISTINCT `batchNo`  from " . PREFIX . T_MACL . " where allotStatus = 2 and  belongID = " . $bid . " and plat = " . $pid . " and machineStatus = 1";
			$query = M()->query($sql);
			if ($query) {
				$ret = array("responseStatus" => 1, "data" => $query);
			} else {
				$ret['responseStatus'] = 300;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 发货
	 * @date: 2018年5月29日 下午5:06:09
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’delivery’, platformID:x,userID:’x’,userPhone:’x’,orderID:x订单ID,courierName:x快递名称,waybillNumber:x快递单号,terminalNo:x终端号} //服务商发货
	 */
	public function delivery($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$params['terminalNo'] = preg_replace("/\n|\t|\r/", "", $params['terminalNo']);
			$data = array(
				"courierName" => $params['courierName'],
				"waybillNumber" => $params['waybillNumber'],
				"terminalNumber" => $params['terminalNo'],
				"consignor" => $bid
			);
			$where = array(
				"id" => $params['orderID'],
				"plat" => $pid
			);
			$field = array(
				"isOrder", "wxIsPay",
				"proid", "bid", "isReceipt"
			);
			$check = M(T_ORDER_TAB)->field($field)->where($where)->find();
			if ($check) {
				if ($check['isOrder'] != 1) {
					//订单异常
					$ret['responseStatus'] = 105;
				} else {
					if ($check['wxIsPay'] == 1) {
						//订单未支付
						$ret['responseStatus'] = 106;
					} else {
						if ($check['isReceipt'] == 2) {
							//已发货
							$ret['responseStatus'] = 107;
						} else {
							if (uRec(T_ORDER_TAB, $data, $where)) {
								$ret['responseStatus'] = 1;
								//更新机具发货时更新信息
								self::update_machine_delivery_info($check['bid'], $params['terminalNo'], $params['orderID'], $check['proid']);
								//存储终端激活查询
								self::terminalSearchManageCord(array(
									"orderid" => $params['orderID'],
									"bid" => $check['bid'],
									"proid" => $check['proid'],
									"terminal" => $data['terminalNumber']
								), $pid);
								//修改订单发货状态
								self::updateDeliverGoodsStat($params['orderID']);
							} else {
								$ret['responseStatus'] = 2002;
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
	 * 更新机具发货时更新信息
	 * @date: 2018年6月6日 下午4:40:36
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $bid 使用者ID
	 * @param: $productID 产品ID
	 * @param: $terminal 终端号
	 * @param: $orderID  订单ID
	 */
	protected static function update_machine_delivery_info($bid, $terminal, $orderID, $productID)
	{
		if (!empty($terminal) && !empty($bid) && !empty($orderID) && !empty($productID)) {
			$where = array(
				"terminalNo" => $terminal,
				"productID" => $productID
			);
			$data = array(
				"useID" => $bid,
				"orderID" => $orderID,
				"useStatus" => 2,
				"useTime" => time()
			);
			uRec(T_MACL, $data, $where);
		}
	}
	/**
	 * 存储终端激活查询
	 * @date: 2018年4月25日 下午1:54:47
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: array $params 参数
	 * --------------------------------------
	 * orderid 订单ID
	 * bid 	         商户ID
	 * proid   产品ID
	 * terminal 终端号
	 * --------------------------------------
	 * @return boolean
	 */
	protected static function terminalSearchManageCord($params, $plat)
	{
		$data = array(
			"proid" => $params['proid'],
			"bid" => $params['bid'],
			"orderid" => $params['orderid'],
			"terminal" => $params['terminal'],
			"isActive" => 1, "plat" => $plat
		);
		if (!cRec("terminal_manage", array(
			"orderid" => $params['orderid'],
			"terminal" => $params['terminal']
		))) {
			if (aRec("terminal_manage", $data)) {
				return true;
			}
		}
		return false;
	}
	/**
	 * 修改订单发货状态
	 * @date: 2018年4月23日 上午10:47:40
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param：int $id 订单ID
	 * @return boolean
	 */
	protected static function updateDeliverGoodsStat($id)
	{
		if (!empty($id)) {
			$data = array(
				"isReceipt" => 2,
				"receiptTime" => time(),
				"isShip" => 1
			);
			if (uRec(T_ORDER_TAB, $data, array(
				"id" => $id
			))) {
				return true;
			}
		}
		return false;
	}
	/**
	 * 发货使用终端列表
	 * @date: 2018年5月29日 下午5:08:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’terminalsmalllist’, platformID:’3’,userID:’x’,userPhone:’x’,productID:X必传,terminalNo:x,start:x(开始),end:x（结束）} //发货查找终端列表
	 */
	public function terminalsmalllist($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$where = array(
				"allotStatus" => 2,
				"plat" => $pid, "belongID" => $bid,
				"productID" => $params['productID'],
				"useStatus" => 1,
				"machineStatus" => 1
			);
			//终端号
			if ($params['terminalNo'])
				$where['terminalNo'] = array(
				"like",
				"%" . $params['terminalNo'] . "%"
			);
				//终端号区间搜索
			if (!empty($params['start']) && !empty($params['end']))
				$where['terminalNo'] = array(
				array("egt", $params['start']),
				array("elt", $params['end'])
			);
			$field = array(
				"id", "terminalNo",
				"(select commodityName from " . PREFIX . T_COMMODITY . " cm where  cm.id  = ml.productID) productName"
			);
			$offset = ($page - 1) * $limit;
			$dao = M(T_MACL . " ml");
			$array = $dao->field($field)->where($where)->select();
			$totalCount = $dao->where($where)->count();
			if ($array) {
				$ret = array(
					"responseStatus" => 1,
					"data" => $array
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 订单列表
	 * @date: 2018年5月29日 下午4:30:50
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’order’, platformID:’3’,userID:’x’,userPhone:’x’,isReceipt:x 默认值 3 必传 已发货传 2, page:x,limit:x} //订单列表
	 */
	public function order($params)
	{
		// 		$params = parent::testParams();
		$pid = $params['platformID'];
		// 		$params['isReceipt'] = 3;
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$dates = empty($params['dates']) ? DEFAULT_SEARCH_DATE : $params['dates'];
			$field = array(
				"id", "ordernum",
				"bid", "orderMoney",
				"depositMoney", "wxpayMoney",
				"proid",
				"from_unixtime(orderTime,'%Y.%m.%d %H:%i:%s') orderTime", 
				// 				"(select busname from " . PREFIX . T_BUS . " u where o.bid = u.id) busname", 
				// 				"(select phone from " . PREFIX . T_BUS . " u where o.bid = u.id) phone", 
				"(select name from " . PREFIX . T_COMM_CATE . " cc where o.procid = cc.id) pcname",
				"(select commodityName from " . PREFIX . T_COMMODITY . " cc where o.proid = cc.id) pname",
				"if(wxIsPay =1 ,'" . NOT_PAY . "',if(isReceipt = 3 ,'" . TO_SEND . "', terminalNumber)) terminalNumber",
				"consignee", "consigneePhone",
				"concat(province,city,area,address) address",
				"isOrder", "isPay", "isDeposit",
				"wxIsPay", "isReceipt",
				"ifnull((select busname from " . PREFIX . T_BUS . " aa where aa.id = getParentVipID(bid)),'无上级') vname",
				"if(wxIsPay = 1,'" . NOT_PAY . "',if(isReceipt = 3, '" . TO_SEND . "', concat('" . COURIER . "：',courierName,' ','" . W_NUM . "：',waybillNumber))) courier",
				"case isOrder when 1 then '" . NORMAL_S . "' when 2  then '" . CANC_S . "' when 3 then '" . INVALID_S . " ' end ostatus",
				"case isPay when 1 then '" . HAVE_PAY . "' when 2 then ' " . NOT_PAY . "' when 3 then '" . PAY_FAIL . "' end pstatus",
				"case isDeposit when 1 then '" . HAVE_PAY . "' when 2  then '" . NOT_PAY . "' when 3 then '支付失败' end dstatus",
				"case wxIsPay when 1 then '" . NOT_PAY . "' when 2  then '" . HAVE_PAY . "' when 3 then '" . PAY_FAIL . "' end wstatus",
				"if(wxIsPay = 1,'" . NOT_PAY . "',from_unixtime(payTime,'%Y.%m.%d %H:%i:%s')) payTime",
				"if(wxIsPay = 1,'" . NOT_PAY . "',case isReceipt when 1 then '" . ORDER_COMPLETED . "' when 2 then '" . BEEN_SHIP . "' when 3 then '" . TO_SEND . "' when  4 then '" . TO_EVALUATE . "' end) rstatus",
				"if(wxIsPay = 1,'" . NOT_PAY . "',if(isReceipt = 3, '" . TO_SEND . "' , from_unixtime(receiptTime,'%Y.%m.%d %H:%i:%s'))) receiptTime",
				"if(wxIsPay = 1,'" . NOT_PAY . "',if(isReceipt = 3, '" . TO_SEND . "' ,case isShip when 1 then '" . WAIT_GOODS . "' when 2 then '" . HAVE_SIGN . "'  end)) sstatus",
				"if(wxIsPay = 1,'" . NOT_PAY . "',if(isReceipt = 3, '" . TO_SEND . "' ,if(isReceipt = 2 ,'" . WAIT_GOODS . "', from_unixtime(shipTime,'%Y.%m.%d %H:%i:%s')))) shipTime",
				"if(wxIsPay = 1,'" . NOT_PAY . "',if(isReceipt = 3, '" . TO_SEND . "',if(isReceipt = 2, '" . WAIT_GOODS . "',case isActivate when 1 then '" . NOT_ACTIVATED . "' when 2 then '" . BEEN_ACTIVATED . "' end ))) jhzt",  //激活状态
				"if(wxIsPay=1,'" . NOT_PAY . "',case isDepositRefund when 1 then '" . NOT_DEPOSIT . "' when 2 then '" . BEEN_DEPOSIT . "' when 3 then '" . NOT_PAY . "' end ) returned"
			);
			//获取下级普通用户
			$sql = "SELECT getChildLst({$bid},{$pid}) bids";
			$query = M()->query($sql);
			$where = array(
				"bid" => array(
					"in",
					$query[0]['bids']
				),
				"plat" => $pid,
				"isOrder" => array("neq", 3),
				"wxIsPay" => 2
			);
			$where['isReceipt'] = $params['isReceipt'];
			if ($params['isReceipt'] == 2) {
				$where['consignor'] = $bid;
			}
			$offset = ($page - 1) * $limit;
			$dao = M(T_ORDER_TAB . " o");
			$array = $dao->field($field)->where($where)->limit($offset, $limit)->order("orderTime DESC")->select();
			$data = array();
			if ($array) {
				foreach ($array as $key => $val) {
					$info = parent::get_user_info($bid);
					$array[$key]['busname'] = $info['busname'];
					$array[$key]['phone'] = $info['phone'];
				}
				$totalCount = $dao->where($where)->count();
				$ret = array(
					"responseStatus" => 1,
					"data" => $array,
					"count" => $totalCount
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
		// 		dump($ret);
	}
	/**
	 * 产品列表
	 * @date: 2018年5月29日 下午3:20:08
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’product’, platformID:x} //产品列表
	 */
	public function product($params)
	{
		$pid = $params['platformID'];
		// 		$pid = 175;
		$where = array(
			"plat" => $pid,
			"status" => 2
		);
		$field = array("id", "commodityName name");
		$array = M(T_COMMODITY)->field($field)->where($where)->select();
		if ($array) {
			$ret = array(
				"responseStatus" => 1,
				"data" => $array
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	/**
	 * 终端列表
	 * @date: 2018年5月29日 下午2:55:52
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’terminal’, platformID:’3’,userID:’x’,userPhone:’x’,productID:x产品ID,batchNo:x批次号 , useStatus：x使用状态,page:x,limit:x,keywords:x} //终端列表
	 */
	public function terminal($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$where = array(
				"allotStatus" => 2,
				"plat" => $pid, "belongID" => $bid,
				"machineStatus" => 1
			);
			//终端号
			if ($params['keywords']) {
				$where['terminalNo'] = array("like", "%" . $params['keywords'] . "%");
			}
			//产品ID
			if ($params['productID']) {
				$where['productID'] = $params['productID'];
			}
			//批次号
			if ($params['batchNo']) {
				$where['batchNo'] = $params['batchNo'];
			}
			//使用状态
			if ($params['useStatus']) {
				$where['useStatus'] = $params['useStatus'];
			}
			$field = array(
				"id", "terminalNo",
				"batchNo", "useID", "useStatus useSta",
				"from_unixtime(useTime,'%Y-%m-%d %H:%i:%s') useTime",
				"from_unixtime(allotTime,'%Y-%m-%d %H:%i:%s') allotTime",
				"case useStatus when 1 then '" . NOT_USE . "' when 2 then '" . BEEN_USE . "' end useStatus",
				"(select commodityName from " . PREFIX . T_COMMODITY . " cm where  cm.id  = ml.productID) productName",
				"if(useStatus = 1, 0 , (select isActive from p_terminal_manage tms where tms.terminal = ml.terminalNo)) isActive",
				"if(useStatus = 1, 0 , (select busname from p_user  where id = useID)) useName",
				"if(useStatus = 1, 0 , (select concat_ws('****',substring(phone,1,3),substring(phone,-4,4))  from p_user  where id = useID)) usePhone",
			);
			$offset = ($page - 1) * $limit;
			$dao = M(T_MACL . " ml");
			$array = $dao->field($field)->where($where)->limit($offset, $limit)->order("id DESC")->select();
			$totalCount = $dao->where($where)->count();
			if ($array) {
				$ret = array(
					"responseStatus" => 1,
					"data" => $array,
					"count" => $totalCount
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		// 		dump($ret);
		return $ret;
	}
	/**
	 * 收益总汇
	 * @date: 2018年5月22日 上午9:51:39
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’summary’, platformID:’3’,userID:’x’,userPhone:’x’,startDate:x,endDate:x}
	 */
	public function summary($params)
	{
		// 		$params = parent::testParams();
		// 		$params['startDate'] = "2018-04-01";
		// 		$params['endDate'] = "2018-05-20";
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$where = '';
			//默认条件
			$where .= "where receiveAN = " . $bid . " and cashType = 'agent'";
			//时间筛选
			$startDate = strtotime(date("Y-m-01"));
			$endDate = strtotime("+1 day", time());
			if (!empty($params['startDate']) && !empty($params['endDate'])) {
				$startDate = strtotime($params['startDate']);
				$endDate = strtotime("+1 day", strtotime($params['endDate']));
			}
			$where .= " and (cashTime >= " . $startDate . " and cashTime < " . $endDate . ")";
			//返现累计次数/次
			$sql = "select COUNT(*) con from p_cash_back_log " . $where;
			$query = M()->query($sql);
			$cashCount = 0;
			if ($query) {
				$cashCount = $query[0]['con'];
			}
			//返现累计人数/人 
			//返现人数去重
			// 			$sql = "select COUNT(DISTINCT outputAN) con from p_cash_back_log " . $where;
			$sql = "select count(*) con from (select DISTINCT outputAN from p_cash_back_log " . $where . ") a";
			// 			echo $sql;
			$query = M()->query($sql);
			$busCount = 0;
			if ($query) {
				$busCount = $query[0]['con'];
			}
			//累计收益
			$sql = "select ifnull(sum(cashMoney),'0.00') sum from p_cash_back_log " . $where;
			$query = M()->query($sql);
			$cashMoney = "0.00";
			if ($query) {
				$cashMoney = parent::subDecimals($query[0]['sum'], 2);
			}
			$ret = array(
				"responseStatus" => 1,
				"cashCount" => $cashCount,
				"busCount" => $busCount,
				"cashMoney" => $cashMoney
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		// 		dump($ret);
		return $ret;
	}
	/**
	 * 收益明细
	 * @date: 2018年5月21日 下午1:42:01
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’earnings’, platformID:’3’,userID:’x’,userPhone:’x,startDate:x 开始时间 , endDate:x 结束时间 , page:x,limit:x}
	 */
	public function earnings($params)
	{
		// 		$params = parent::testParams();
		// 		$params['startDate'] = "2018-05-20";
		// 		$params['endDate'] = "2018-05-20";
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$where = array(
				"receiveAN" => $bid,
				"cashType" => "agent",
				"isAddWallet" => 1
			);
			if (!empty($params['startDate']) && !empty($params['endDate'])) {
				$where['cashTime'] = array(
					array(
						"egt", strtotime($params['startDate'])
					),
					array(
						"lt", strtotime("+1 day", strtotime($params['endDate']))
					)
				);
			}
			$offset = ($page - 1) * $limit;
			$field = array(
				"cashMoney",
				"tableName", "uniqueIndex",
				"from_unixtime(cashTime,'%Y-%m-%d %H:%i:%s') cashTime",
				"ifnull((select name from " . PREFIX . T_CERT . "  aac where aac.bid = cl.outputAN),(select busname from " . PREFIX . T_BUS . " u where u.id = cl.outputAN)) outbus"
			);
			$array = M(T_CABL . " cl")->field($field)->where($where)->limit($offset, $limit)->order("cashTime DESC")->select();
			$data = array();
			if ($array) {
				for ($i = 0; $i < count($array); $i++) {
					$data[$i]['outbus'] = $array[$i]['outbus'];
					//获取交易金额、交易卡类型
					$cash_back_info = self::pos_data($array[$i]['tableName'], $array[$i]['uniqueIndex']);
					if ($cash_back_info) {
						$data[$i]['trade'] = $cash_back_info['tradeAmt'];
						$data[$i]['cardType'] = "";
					}
					$data[$i]['cashMoney'] = $array[$i]['cashMoney'];
					$data[$i]['cashTime'] = $array[$i]['cashTime'];
				}
				$totalCount = M(T_CABL)->where($where)->count();
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
		// 		dump($ret);
		return $ret;
	}
	/**
	 * 获取交易金额、交易卡类型
	 * @date: 2018年5月21日 下午2:39:08
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $table 数据表 $uindex 唯一索引
	 * @return: array
	 */
	protected static function pos_data($table, $uindex)
	{
		if (!empty($table) && !empty($uindex)) {
			$field = array("tradeAmt");
			$where = array(
				"tradeOrderNo" => $uindex
			);
			$re = M($table)->field($field)->where($where)->find();
			if ($re) {
				return $re;
			}
		}
		return false;
	}
	/**
	 * 获取下级数|收益总金额
	 * @date: 2018年5月18日 下午3:27:15
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’agent’,requestKeywords:’getnum’, platformID:’3’,userID:’x’,userPhone:’x’}
	 */
	public function getnum($params)
	{
		// 		$params = parent::testParams();
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$data = M(T_BUS)->field("id,busname,phone,parent")->where(array(
				"plat" => $pid, "status" => 1
			))->select();
			//获取下级数
			$re = self::get_team_menber($data, $bid);
			$num = 0;
			$money = "0.00";
			if ($re) {
				$num = count($re);
			}
			//收益总金额
			$cash = self::get_cash_money($bid);
			if ($cash) {
				$money = $cash;
			}
			$ret = array(
				"responseStatus" => 1,
				"teamnum" => $num,
				"cashMoney" => $money
			);
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 获取返现金额
	 * @date: 2018年5月18日 下午3:21:45
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:
	 * @return:
	 */
	protected static function get_cash_money($bid)
	{
		if (!empty($bid)) {
			$sql = "select sum(cashMoney) sum from p_cash_back_log where  receiveAN =" . $bid . " and cashType = 'agent' and isAddWallet = 1";
			$query = M()->query($sql);
			if ($query) {
				return parent::subDecimals($query[0]['sum'], 2);
			}
		}
		return false;
	}
	/**
	 * 获取某商户无限下级方法
	 * @date: 2018年5月18日 下午2:59:19
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $data是所有商户数据表,$bid 商户ID
	 * @return: array
	 */
	protected static function get_team_menber($data, $bid)
	{
		if (!empty($bid)) {
			if (!is_array($data)) {
				return false;
			}
			$teams = array(); //最终结果
			$mids = array($bid); //第一次执行时候的用户id
			do {
				$othermids = array();
				$state = false;
				foreach ($mids as $valueone) {
					foreach ($data as $key => $valuetwo) {
						if ($valuetwo['parent'] == $valueone) {
							$teams[] = $valuetwo[id]; //找到我的下级立即添加到最终结果中
							$othermids[] = $valuetwo['id']; //将我的下级id保存起来用来下轮循环他的下级
							array_splice($members, $key, 1); //从所有会员中删除他
							$state = true;
						}
					}
				}
				$mids = $othermids; //foreach中找到的我的下级集合,用来下次循环
			} while ($state == true);
			return $teams;
		}
		return false;
	}
	// 组织架构
	public function organization($params)
	{
		//接收本身id找下级(记录下级总数)然后找到下级后统计下级总数 & 是否服务商
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$name = parent::getField("user", "id", $bid, "busname");
			$current_id = $params['current_id'];
			if (!empty($current_id)) {
				$bid = $current_id;
			}
			//$list = sRec("user",array("parent"=>$bid,"status"=>1,"plat"=>$pid),"regisTime desc","","","id uid,concat(substring(busname,1,1),'**') name,concat(substring(phone,1,3),'****',substring(phone,8,4)) phone,if(level=2,1,0) vip,(select count(*) from p_user where parent=uid and status=1) counts");
			$list = sRec("user", array(
				"parent" => $bid, "status" => 1,
				"plat" => $pid
			), "regisTime desc", "", "", "id uid,ifnull((select name from  p_verified  v where bid = uid),busname) name,ifnull(concat(substring(phone,1,3),'****',substring(phone,8,4)),'') phone ,if(level=2,1,0) vip,(select count(*) from p_user where parent=uid and status=1) counts");
			if ($list) {
				// 				foreach ($list as $key => $val){
				// 					$list[$key]['']
				// 				}
				$ret = array(
					"responseStatus" => 1,
					"data" => $list
				);
			} else {
				$ret['responseStatus'] = 300;
			}
			$ret['name'] = $name;
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
}